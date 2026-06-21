# Student Accommodation — Project Report

## Overview
A responsive student accommodation web application (PG listing) built with HTML, CSS, Bootstrap, PHP, MySQL, JavaScript (AJAX) and a React shortlist component.

## Architecture
- Frontend: PHP templates in `public/`, Bootstrap 5, jQuery for AJAX.
- Backend: PHP API in `backend/api.php` connecting to MySQL via `backend/connection.php`.
- Database: MySQL schema in `db/schema.sql` (tables: users, properties, amenities, property_amenities, interested_users).
- React: Shortlist implemented as a React app built with Vite into `public/react`.

## Features
- Property listing with filters (city, gender, max price) and search.
- Server-side pagination and search.
- Property detail page with image gallery, amenities, description, rating and interest toggle.
- User signup/login (sessions) and profile page (name, email, phone).
- AJAX-based interest toggle and filters (no page reload).
- React-based shortlist component (built assets served from `public/react`).

## How to run locally
1. Create and import DB:
```sql
CREATE DATABASE student_accommodation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- then import db/schema.sql
```
2. Update DB credentials in `backend/connection.php`.
3. Ensure `public/uploads` exists and contains sample images or update property `images` paths in DB.
4. Start PHP server:
```bash
php -S localhost:8000 -t public
```
5. Open http://localhost:8000

## Deliverables
Please provide the following screenshots (place them in `deliverables/`):
- `listing.png` — Property Listing Page (with filters visible)
- `detail.png` — Property Detail Page (gallery + amenities)
- `ajax-interaction.png` — Demonstration of AJAX filtering or interest toggle

## Notes on Deployment
- Deploy to any PHP+MySQL hosting; import `db/schema.sql`, update DB credentials.
- For production, build the React app (`react-app`) and ensure `public/react` assets are uploaded.

*** End of report
