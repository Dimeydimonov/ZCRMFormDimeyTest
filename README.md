# zoho crm form - test assignment

web form for creating deals and accounts in zoho crm.
frontend - vue.js, backend - laravel, all in docker.

# requirements
- docker & docker compose
- zoho crm trial account (https://www.zoho.com/crm/signup.html)

# zoho setup
1. go to https://api-console.zoho.com
2. "Add Client" → "Server-based Applications"
3. redirect uri: `http://localhost:8000/zoho/callback`
4. copy client id and client secret

get refresh token - open in browser:
https://accounts.zoho.eu/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=YOUR_CLIENT_ID&response_type=code&access_type=offline&redirect_uri=http://localhost:8000/zoho/callback

after redirect, copy the `code` parameter from url and run:

bash
curl -X POST https://accounts.zoho.eu/oauth/v2/token \
  -d "grant_type=authorization_code" \
  -d "client_id=YOUR_CLIENT_ID" \
  -d "client_secret=YOUR_CLIENT_SECRET" \
  -d "redirect_uri=http://localhost:8000/zoho/callback" \
  -d "code=YOUR_CODE"

save the `refresh_token` from response.

# setup
bash
cp backend/.env.example backend/.env

edit `backend/.env` - fill in ZOHO_CLIENT_ID, ZOHO_CLIENT_SECRET, ZOHO_REFRESH_TOKEN.

# run
bash
docker compose up -d --build
docker exec zcrmformdimeytest-php-1 composer install
docker exec zcrmformdimeytest-php-1 php artisan key:generate
docker exec zcrmformdimeytest-php-1 php artisan migrate --force

frontend: http://localhost:5173
backend api: http://localhost:8000

# how it works
1. fill in the form (deal name, stage, account info)
2. click "Create Deal & Account"
3. backend creates account in zoho crm first
4. then creates deal and links it to the account
5. token refreshes automatically (cached for 1 hour)
