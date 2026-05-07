<?php

	namespace App\Http\Controllers;

	use App\Services\ZohoTokenService;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Log;

	class ZohoController extends Controller
	{
		protected $tokenService;

		public function __construct(ZohoTokenService $tokenService)
		{
			$this->tokenService = $tokenService;
		}
		public function submit(Request $request)
		{
			$validated = $request->validate([
				'deal_name' => 'required|string|max:255',
				'deal_stage' => 'required|string|max:255',
				'account_name' => 'required|string|max:255',
				'account_website' => 'nullable|url|max:255',
				'account_phone' => 'required|string|max:50',
			]);


			try {
				$token = $this->tokenService->getAccessToken();
				$apiDomain = env('ZOHO_API_DOMAIN');
				$accountData = [
					'Account_Name' => $validated['account_name'],
					'Phone' => $validated['account_phone'],
				];
				if (!empty($validated['account_website'])) {
					$accountData['Website'] = $validated['account_website'];
				}

				$accountResp = Http::withToken($token)
					->post("{$apiDomain}/crm/v2/Accounts", [
						'data' => [$accountData]
					]);

				$accountResult = $accountResp->json();
				Log::info('Zoho account response', $accountResult ?? []);

				if (isset($accountResult['code']) && $accountResult['code'] == 'INVALID_TOKEN') {
					Log::info('Token expired, getting new one...');
					$this->tokenService->clearToken();
					$token = $this->tokenService->getAccessToken();
					$accountResp = Http::withToken($token)
						->post("{$apiDomain}/crm/v2/Accounts", [
							'data' => [$accountData]
						]);
					$accountResult = $accountResp->json();
					Log::info('Zoho account response (retry)', $accountResult ?? []);
				}

				if (!isset($accountResult['data'][0]['details']['id'])) {
					return response()->json([
						'success' => false,
						'message' => 'Failed to create Account in Zoho',
						'error' => $accountResult
					], 422);
				}
				$accountId = $accountResult['data'][0]['details']['id'];
				Log::info('Account created with id: ' . $accountId);

				$dealResp = Http::withToken($token)
					->post("{$apiDomain}/crm/v2/Deals", [
						'data' => [[
							'Deal_Name' => $validated['deal_name'],
							'Stage' => $validated['deal_stage'],
							'Account_Name' => ['id' => $accountId],
						]]
					]);

				$dealResult = $dealResp->json();
				Log::info('Zoho deal response', $dealResult ?? []);
				if (!isset($dealResult['data'][0]['details']['id'])) {
					return response()->json([
						'success' => false,
						'message' => 'Account created but failed to create Deal',
						'error' => $dealResult
					], 422);
				}
				$dealId = $dealResult['data'][0]['details']['id'];
				Log::info('Deal created with id: ' . $dealId);
				return response()->json([
					'success' => true,
					'message' => 'Deal and Account created successfully!',
					'account_id' => $accountId,
					'deal_id' => $dealId,
				]);
			} catch (\Exception $e) {
				Log::error('Zoho submit error: ' . $e->getMessage());
				return response()->json([
					'success' => false,
					'message' => 'Server error: ' . $e->getMessage()
				], 500);
			}
		}
	}
