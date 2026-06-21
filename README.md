# Student Accommodation

A responsive student accommodation web application (PG listing) built with HTML, CSS, Bootstrap, PHP, MySQL, JavaScript, AJAX and a small React component.

## Structure

- `public/` - frontend pages and assets
- `backend/` - PHP API and DB connection
- `db/schema.sql` - database schema and sample data
- `react/` - small React component used on the frontend

## Setup

1. Create a MySQL database, e.g. `student_accommodation`.
2. Import `db/schema.sql` into your MySQL instance.
3. Update DB credentials in `backend/connection.php`.
4. Serve the `public/` folder via a PHP-enabled webserver (Apache, Nginx + PHP-FPM).

## Try locally (PHP built-in server)

```bash
php -S localhost:8000 -t public
```

Then open `http://localhost:8000` in your browser.

## Push to GitHub

1. Create a remote repository on GitHub (via website or `gh` CLI).
2. Add remote and push:

```bash
git remote add origin https://github.com/your-username/student-accommodation.git
git branch -M main
git push -u origin main
```

If you prefer the GitHub API or automated creation, provide a token and I can help create the remote.

## Deployment

Deploy to any PHP-capable hosting (shared hosting, VPS). For modern deployments consider using services that support PHP and MySQL.

## Push to GitHub

1. Create a remote repository on GitHub (via website or `gh` CLI).
2. Add remote and push:

```bash
git remote add origin https://github.com/your-username/student-accommodation.git
git branch -M main
git push -u origin main
```

If you prefer the GitHub API or automated creation, provide a token and I can help create the remote.

## Deliverables
- Live project URL (after deployment)
- GitHub repository link
- `db/schema.sql`
- Screenshots: Property Listing, Property Detail, AJAX interactions
- Brief explanation document

## Notes
- Auth uses PHP sessions; replace with JWT or stronger session handling for production.
- React component is loaded via CDN/Babel for demo; use a proper build setup for production.

## New features added
- Server-side pagination and search on the listing page (`page`, `per_page`, `q`).
- User profile page at `/profile.php` with editable `name` and `phone`.
- Signup accepts `phone` and stores it in the database.
- React shortlist converted to a Vite-built app; build artifacts are placed in `public/react`.

## Building React locally
From the `react-app` folder run:

```bash
npm install
npm run build
```

This outputs files into `public/react` which are automatically loaded by the main page.

