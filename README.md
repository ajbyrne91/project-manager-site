# Project Manager

A small PHP and MySQL learning project for creating, browsing and managing projects through a web interface. It demonstrates account registration, session-based login, relational data modelling and CRUD operations without a framework.

This repository is an educational portfolio example. It is intended for local use with fictional data and still has the limitations listed below.

## What it does

- Registers users and hashes passwords with PHP's `password_hash`; login uses `password_verify`.
- Lists projects and searches by a partial title or an exact start date (`YYYY-MM-DD`).
- Records a title, start and end dates, description and project phase.
- Lets authenticated users create projects and edit or delete their own projects; ownership is checked in the database queries.
- Uses prepared statements for user-supplied query values, escapes displayed project titles and protects POST forms with a session CSRF token.
- Shows an owner's username on project details; account email addresses are not displayed there.

Browsing is shared: all visitors can see every project's details. Login controls changes, rather than providing private project workspaces.

## Skills demonstrated

PHP request handling, HTML forms, CSS, sessions, password hashing, SQL joins, foreign keys, prepared statements, basic access control and troubleshooting across a browser, application and database.

## Run locally

Requirements:

- PHP 8.x with `mysqli` and `mysqlnd` (`mysqli_stmt::get_result` is used).
- A local MySQL server and its command-line client.
- A browser. There are no Composer or JavaScript dependencies.

1. Clone the repository and enter its directory.

   ```sh
   git clone https://github.com/ajbyrne91/project-manager-site.git
   cd project-manager-site
   ```

2. Start your local MySQL service and import the empty schema. This creates `project_manager_db`; it contains no seed accounts or personal data. Use an account that has permission to create the database and tables.

   ```sh
   mysql -u root -p < database.sql
   ```

3. Set connection details in your terminal before starting PHP. For your own setup, use a dedicated local database user with access to `project_manager_db`. The example below uses Bash to read that user's password without displaying it; do not put real credentials in committed files.

   ```sh
   export DB_HOST=127.0.0.1
   export DB_PORT=3306
   export DB_NAME=project_manager_db
   export DB_USER=your_local_database_user
   read -r -s DB_PASSWORD
   export DB_PASSWORD
   php -S 127.0.0.1:8000
   ```

   `config.php` reads these environment variables. If omitted, its original local-development defaults are `localhost`, port `3306`, database `project_manager_db`, user `root` and an empty password. Change `DB_NAME` only if you also change the database name in the import script.

4. Open [http://127.0.0.1:8000](http://127.0.0.1:8000), register a fictional account and log in. Add a sample project with both dates, browse or search for it, then try editing and deleting it.

The PHP development server is for local testing. Keep this demo bound to localhost.

## Repository guide

| File | Purpose |
| --- | --- |
| `config.php` | Starts the session, checks POST CSRF tokens and opens the database connection |
| `database.sql` | Creates the database, users and projects tables |
| `register.php`, `login.php`, `logout.php` | Account registration and session lifecycle |
| `index.php`, `projects.php` | Project lists and search |
| `project_details.php` | Project details and owner username |
| `add_project.php`, `edit_project.php`, `delete_project.php` | Project creation and owner-controlled updates/deletion |
| `style.css` | Shared page styling |

The `users` table has a unique username and email plus a password hash. Each `projects` row references its owner's user ID. The foreign key cascades deletion and user-ID updates. Phases are `design`, `development`, `testing`, `deployment` and `complete`.

## Current limitations and next steps

Validation during the repository cleanup: all PHP files passed syntax checks, and POST requests with missing, invalid or non-string CSRF tokens returned HTTP 403. A complete database-backed registration/login/project lifecycle test remains unverified because the available local MySQL server failed during initialization.

- Server-side input validation is basic. Date ordering, field length limits, email validation, password strength and allowed phase values need explicit validation and friendly error messages.
- There is no login rate limiting, password reset or email verification.
- Session cookie hardening, HTTPS deployment settings and production error handling need review. Logout currently uses a GET request.
- Projects are visible to everyone; this is unsuitable for confidential work or real customer information.
- The schema is an initial setup script, not a migration system. Re-importing it after the tables exist will fail.
- There is no automated test suite, pagination or accessibility audit.

Useful next improvements would be stricter validation, a consistent shared layout and automated checks for authentication, ownership and project lifecycle behaviour.
