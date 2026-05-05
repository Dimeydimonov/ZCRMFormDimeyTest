 Zoho CRM Integration Form   Test Assignment

A full stack web application integrating with Zoho CRM API to create deals and accounts. Features a Vue.js 3 frontend form with Laravel 11 backend API, all containerized with Docker for seamless deployment and development.

[Vue.js](https://img.shields.io/badge/Vue.js 3.4 4FC08D?style=flat square&logo=vue.js)
[Laravel](https://img.shields.io/badge/Laravel 11.31 FF2D20?style=flat square&logo=laravel)
[PHP](https://img.shields.io/badge/PHP 8.2+ 777BB4?style=flat square&logo=php)
[Docker](https://img.shields.io/badge/Docker Compose 2496ED?style=flat square&logo=docker)
[Zoho CRM](https://img.shields.io/badge/Zoho CRM%20API 04264D?style=flat square)

 Project Overview

This application demonstrates seamless integration with Zoho CRM through their REST API. Users can fill out a form to create new accounts and deals in Zoho CRM, with the backend handling OAuth authentication, token management, and API communication.

 Key Features

 Frontend (Vue.js 3)
  Modern Vue.js 3   Composition API with reactive forms
  Form Validation   Real time client side validation
  Axios Integration   HTTP client for API communication
  Responsive Design   Mobile first responsive layout
  Error Handling   User friendly error messages
  Loading States   Visual feedback during API calls

 Backend (Laravel 11)
  Zoho CRM API Integration   Complete OAuth 2.0 flow
  Token Management   Automatic token refresh with caching
  Laravel Sanctum   API authentication and security
  RESTful API   Clean API endpoints for frontend
  Error Handling   Comprehensive exception management
  Database Integration   MySQL for data persistence

 DevOps & Infrastructure
  Docker Compose   Multi container development environment
  Nginx   Web server configuration
  MySQL 8.0   Relational database
  Vite   Modern frontend build tool
  Hot Reloading   Development environment with live updates

 Tech Stack

 Frontend
  Vue.js: 3.4+ with Composition API
  Build Tool: Vite 5.0
  HTTP Client: Axios 1.6
  Styling: CSS3 with responsive design

 Backend  
  PHP: 8.2+
  Framework: Laravel 11.31
  Authentication: Laravel Sanctum
  Database: MySQL 8.0
  API: RESTful with JSON responses

 Infrastructure
  Containerization: Docker Compose
  Web Server: Nginx (Alpine)
  Database: MySQL 8.0
  Node.js: 20 (for frontend build)

 Quick Start

 Prerequisites
  Docker and Docker Compose
  Zoho CRM Trial Account ([Sign up here](https://www.zoho.com/crm/signup.html))

 Zoho CRM Setup

Create OAuth Application
     Go to [Zoho API Console](https://api console.zoho.com)
     Click "Add Client" → "Server based Applications"
     Set redirect URI: `http://localhost:8000/zoho/callback`
     Note down Client ID and Client Secret

Generate Authorization Code
   
   Open this URL in your browser (replace YOUR_CLIENT_ID):
   
   https://accounts.zoho.eu/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=YOUR_CLIENT_ID&response_type=code&access_type=offline&redirect_uri=http://localhost:8000/zoho/callback
   

Get Refresh Token
   
   After authorization redirect, copy the `code` parameter and run:
   bash
   curl  X POST https://accounts.zoho.eu/oauth/v2/token \
      d "grant_type=authorization_code" \
      d "client_id=YOUR_CLIENT_ID" \
      d "client_secret=YOUR_CLIENT_SECRET" \
      d "redirect_uri=http://localhost:8000/zoho/callback" \
      d "code=YOUR_CODE"
   
   
   Save the `refresh_token` from the response.

 Application Setup

Clone and Setup Environment
bash
git clone <repository url>
cd ZCRMFormDimeyTest
cp backend/.env.example backend/.env


Configure Environment
Edit `backend/.env` with your Zoho credentials:
env
ZOHO_CLIENT_ID=your_client_id_here
ZOHO_CLIENT_SECRET=your_client_secret_here  
ZOHO_REFRESH_TOKEN=your_refresh_token_here
ZOHO_REGION=https://accounts.zoho.eu   or .com for US
ZOHO_API_DOMAIN=https://www.zohoapis.eu   or .com for US


Build and Start Services
bash
docker compose up  d   build


Install Dependencies and Setup
bash
 Backend setup
docker exec zcrmformdimeytest php 1 composer install
docker exec zcrmformdimeytest php 1 php artisan key:generate
docker exec zcrmformdimeytest php 1 php artisan migrate   force

 Frontend is auto installed via Docker


Access the Application
  Frontend: http://localhost:5173
  Backend API: http://localhost:8000

 How It Works

 Application Flow
1. User fills form   Deal name, stage, account information
2. Frontend validation   Real time form validation
3. API submission   Axios posts data to Laravel backend
4. Token refresh   Backend automatically refreshes Zoho access token
5. Account creation   Laravel creates account in Zoho CRM first
6. Deal creation   Laravel creates deal and links to the account
7. Response handling   Success/error feedback to user

 API Workflow

Vue.js Form → Laravel API → Zoho OAuth → Zoho CRM API
     ↓             ↓            ↓           ↓
Form Data → JSON Request → Access Token → Account/Deal
     ↑             ↑            ↑           ↑
Success UI ← JSON Response ← Token Cache ← CRM Response


 Project Structure


── docker compose.yml           Multi container configuration
── docker/
   ── nginx/
      ── default.conf        Nginx server configuration
   ── php/
       ── Dockerfile          PHP FPM container setup
── backend/                     Laravel 11 API
   ── app/
      ── Http/
         ── Controllers/
             ── ZohoController.php   Main API controller
      ── Services/           Business logic layer
   ── config/                 Laravel configuration
   ── database/               Migrations and models
   ── routes/
      ── api.php            API routes definition
   ── .env.example           Environment template
── frontend/                   Vue.js 3 SPA
   ── src/
      ── App.vue            Main Vue component
      ── main.js            Application entry point
   ── package.json           Frontend dependencies
   ── vite.config.js         Vite build configuration
── README.md


 API Endpoints

 POST /api/zoho/submit
Create a new deal and account in Zoho CRM.

Request Body:
json
{
  "deal_name": "New Business Opportunity",
  "stage": "Qualification",
  "account_name": "Acme Corporation", 
  "account_website": "https://acme.com",
  "account_phone": "+1 555 0123",
  "contact_name": "John Smith",
  "contact_email": "john@acme.com",
  "contact_phone": "+1 555 0124"
}


Success Response (200):
json
{
  "success": true,
  "message": "Deal and Account created successfully",
  "data": {
    "account_id": "3652397000000346012",
    "deal_id": "3652397000000346015",
    "account_name": "Acme Corporation",
    "deal_name": "New Business Opportunity"
  }
}


Error Response (400/500):
json
{
  "success": false,
  "message": "Error creating account: Invalid data provided",
  "errors": {
    "account_name": ["The account name field is required."]
  }
}


  Backend Architecture

 Controller Layer
php
class ZohoController extends Controller
{
    public function submit(Request $request)
    {
        // Validates incoming request
        // Calls Zoho service methods
        // Returns JSON response
    }
}


 Service Layer
php
class ZohoService
{
    public function createAccountAndDeal(array $data)
    {
        // Handles Zoho API authentication
        // Creates account first, then deal
        // Manages token refresh automatically
    }
}


 Authentication Flow
  Token Storage   Cached for 50 minutes
  Auto refresh   Transparent token renewal
  Error Handling   Graceful API failure management
  Rate Limiting   Respects Zoho API limits

 Frontend Features

 Vue.js Components
  Reactive Forms   Two way data binding
  Validation   Real time form validation
  Loading States   Visual feedback during submission
  Error Handling   User friendly error display

 Form Fields
  Deal Information   Name and sales stage
  Account Details   Company information
  Contact Data   Primary contact details
  Validation Rules   Required fields and format checking

 Security Features

  Laravel Sanctum   API authentication tokens
  CSRF Protection   Cross site request forgery prevention  
  Input Validation   Server side data validation
  OAuth 2.0   Secure Zoho CRM authentication
  Environment Variables   Sensitive data protection
  CORS Handling   Cross origin resource sharing

 Development Commands

 Docker Operations
bash
 Start all services
docker compose up  d

 Rebuild containers
docker compose up  d   build

 View logs
docker compose logs  f

 Stop services
docker compose down


 Backend Commands
bash
 Install PHP dependencies
docker exec zcrmformdimeytest php 1 composer install

 Generate application key
docker exec zcrmformdimeytest php 1 php artisan key:generate

 Run migrations
docker exec zcrmformdimeytest php 1 php artisan migrate

 Clear caches
docker exec zcrmformdimeytest php 1 php artisan cache:clear


 Frontend Commands
bash
 Install dependencies (automatic via Docker)
cd frontend && npm install

 Run development server
cd frontend && npm run dev

 Build for production
cd frontend && npm run build


 Potential Enhancements

 Backend Improvements
  Queue System   Background processing for API calls
  Logging   Comprehensive audit trail
  Testing   PHPUnit test suite
  Caching   Redis for better performance
  Validation   Enhanced form validation rules

 Frontend Enhancements
  TypeScript   Type safety for larger applications
  State Management   Pinia for complex state
  UI Framework   Tailwind CSS or Vuetify
  Testing   Vue Test Utils integration
  PWA Features   Offline capability

 Integration Features
  Webhooks   Real time Zoho CRM updates
  Bulk Operations   Multiple record creation
  File Uploads   Document attachments
  Advanced Search   CRM data querying
  Sync Service   Bidirectional data sync

 DevOps Improvements
  CI/CD Pipeline   Automated testing and deployment
  Monitoring   Application performance monitoring
  SSL/TLS   HTTPS configuration
  Backup Strategy   Database backup automation
  Scaling   Load balancing and horizontal scaling

 Testing

 Backend Testing
bash
 Run PHPUnit tests
docker exec zcrmformdimeytest php 1 php artisan test

 Run with coverage
docker exec zcrmformdimeytest php 1 php artisan test   coverage


 API Testing
bash
 Test Zoho submission endpoint
curl  X POST http://localhost:8000/api/zoho/submit \
   H "Content Type: application/json" \
   d '{
    "deal_name": "Test Deal",
    "stage": "Prospecting",
    "account_name": "Test Company"
  }'


  Contributing

1. Fork the repository
2. Create a feature branch (`git checkout  b feature/amazing feature`)
3. Commit changes (`git commit  m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing feature`)
5. Open a Pull Request

 Development Guidelines
  Follow PSR 12 coding standards for PHP
  Use Vue.js 3 Composition API patterns
  Write comprehensive tests
  Update documentation for new features
  Follow conventional commit messages

 License

This project is open sourced software licensed under the [MIT license].

   

Built with modern web technologies and Zoho CRM integration