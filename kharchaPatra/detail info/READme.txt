# kharchaPatra — Personal Expense Tracker
BCA 4th Semester Project

A web app to track income, expenses, savings, and view financial reports.
Built with HTML, CSS, JavaScript, PHP, and MySQL — designed to match the
Figma UI (sage-green theme, sidebar dashboard, charts, and forms).

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript, Chart.js (via CDN)
- **Backend:** PHP (procedural, mysqli with prepared statements)
- **Database:** MySQL
- **Tools:** XAMPP, Figma

## Setup Instructions (XAMPP)

1. **Install XAMPP** (if not already) and start **Apache** and **MySQL**.

2. **Copy the project folder** `kharchapatra/` into your XAMPP `htdocs` directory:
   - Windows: `C:\xampp\htdocs\kharchapatra`
   - macOS: `/Applications/XAMPP/htdocs/kharchapatra`
   - Linux: `/opt/lampp/htdocs/kharchapatra`

3. **Create the database:**
   - Open `http://localhost/phpmyadmin`
   - Click **Import** → choose `database/kharchapatra.sql` → click **Go**
   - This creates the `kharchapatra` database with all required tables
     (`users`, `categories`, `income`, `expenses`, `savings`).

4. **Check the DB connection settings** in `config/db.php`.
   The defaults match a standard XAMPP install (`root` user, no password).
   Edit these if your MySQL setup is different.

5. **Run the app:**
   Open `http://localhost/kharchapatra/` in your browser.
   You'll land on the login page — click **Sign Up** to create your first
   account. A few default expense categories (Food, Transport, Rent,
   Utilities, Entertainment, Other) are created automatically for every
   new user.

## Pages / Features

| Page | Description |
|---|---|
| `login.php` | Email + password login |
| `signup.php` | Register with first name, last name, email, password |
| `dashboard.php` | Balance/Income/Expense/Saving cards, 7-day line & bar charts, quick-add income/expense forms |
| `income.php` | Full income list with add / edit / delete (modal form) |
| `expenses.php` | Full expense list with category, note, add / edit / delete |
| `category.php` | Manage expense categories (add / edit / delete) |
| `reports.php` | Filter by date range, month, or year — view totals and full transaction list |
| `settings.php` | Update profile info and change password |
| `about.php` | About Us page — mission, vision, story, tech stack, journey |

## Security Notes
- Passwords are hashed with PHP's `password_hash()` / verified with `password_verify()`.
- All SQL queries use prepared statements (mysqli) to prevent SQL injection.
- Every protected page checks the session via `includes/auth.php` before loading.
- All user-submitted values are escaped with `htmlspecialchars()` before being echoed.

## Folder Structure
```
kharchapatra/
├── includes/db.php              # database connection
├── database/kharchapatra.sql  # schema
├── includes/                  # auth guard, sidebar, topbar
├── css/style.css              # all styling (design tokens from Figma)
├── js/script.js               # shared front-end behavior
├── login.php / signup.php / logout.php
├── dashboard.php / get_chart_data.php
├── income.php / expenses.php / category.php
├── reports.php / settings.php / about.php
└── index.php                  # entry router
```