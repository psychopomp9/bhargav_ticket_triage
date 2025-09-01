# Smart Ticket Triage & Dashboard

A Laravel 12 + Vue 3 (Options API) SPA for submitting, classifying, viewing, and editing support tickets, with a small analytics dashboard.

## Quick Start
1. **Clone Project**
git clone https://github.com/psychopomp9/bhargav_ticket_triage.git
cd bhargav_ticket_triage

2. **Install PHP dependencies**
composer install

3. **Install Node dependencies**
npm install

4. **Set up environment file**
cp .env.example .env
php artisan key:generate
# update DB_* vars with your database connection
# Add your OPENAI_API_KEY if available, or leave OPENAI_CLASSIFY_ENABLED=false

5. **Run migrations & seed database**
php artisan migrate --seed

6. **Prepare queue (for classification jobs)**
php artisan queue:work

7. **Start front-end server**
npm run dev

8. **Start Laravel server**
php artisan serve
# Visit http://127.0.0.1:8000


# Assumptions & Trade-offs
* Minimal dependencies: openai-php/laravel + chart.js. No CSS frameworks; BEM only.
* SPA mounted from Blade, Vite bundles assets. Catch-all route serves SPA.
* category_overridden preserves manual categories while still updating AI explanation/confidence.
* Simple per-minute rate limit on classify endpoint via named throttle.
* CSV export implemented client-side.


# What I’d do with more time
* Add authentication/roles