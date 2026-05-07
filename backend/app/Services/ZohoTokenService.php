<?php

	namespace App\Services;

	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\Log;

	class ZohoTokenService
	{
		public function getAccessToken(): string
		{
			return Cache::remember('zoho_access_token', 3000, function () {
				return $this->refreshAccessToken();
			});
		}
		public function clearToken()
		{
			Cache::forget('zoho_access_token');
			Log::info('Cleared zoho token cache');
		}
		private function refreshAccessToken(): string
		{
			$response = Http::asForm()->post(env('ZOHO_ACCOUNTS_URL') . '/oauth/v2/token', [
				'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
				'client_id' => env('ZOHO_CLIENT_ID'),
				'client_secret' => env('ZOHO_CLIENT_SECRET'),
				'grant_type' => 'refresh_token',
			]);
			$data = $response->json();
			Log::info('Zoho token refresh response', $data ?? []);

			if (!isset($data['access_token'])) {
				throw new \Exception('Failed to refresh Zoho token: ' . json_encode($data));
			}
			return $data['access_token'];
		}
	}
