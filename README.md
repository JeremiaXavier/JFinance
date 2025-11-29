# JFinance – PHP Finance Management System

JFinance is a private, web-based finance management system built with PHP, MySQL, Tailwind CSS, and TCPDF.  
It follows a clean, portal-style UI (similar to institutional/government dashboards) but is branded for private/corporate use.

## ✨ Features

- User authentication (login, register, logout)
- Dashboard:
  - Net balance
  - Total income, expenses, and debt
  - Recent transactions table
- Income / Expense / Debt tracking
- Category master (with `income` / `expense` types)
- Budget Control Center:
  - Per-category budget limits
  - Utilization bar (Under / Near / Over limit)
- Official Letter Generator:
  - Corporate letterhead with logo
  - HTML A4 preview page
  - PDF export using TCPDF

## 🧱 Tech Stack

- PHP 7+ / 8+
- MySQL / MariaDB
- Tailwind CSS (via CDN)
- TCPDF (PHP PDF library)
- Plain PHP + MySQLi (no heavy framework)

## 📂 Project Structure (key files)

- `config.php`  
  Database connection, session start, helper functions (`is_logged_in()`, `redirect()`, etc.).

- `layout.php`  
  Shared layout:
  - Top header bar with system name
  - Horizontal navigation
  - Left sidebar
  - Main content slot (`$page_content`).

- Auth:
  - `index.php` – redirects to login or dashboard.
  - `login.php` – user login.
  - `register.php` – user registration.
  - `logout.php` – session logout.

- Core modules:
  - `dashboard.php` – summary cards + recent transactions.
  - `income.php` – income entries.
  - `expense.php` – expense entries.
  - `debt.php` – debt / liability entries.
  - `category.php` – category master (`income` / `expense`).
  - `budget.php` – Budget Control Center.

- Letter system:
  - `letter_generator.php` – compose letter (recipient, subject, body, date).
  - `letter_preview.php` – A4-style HTML preview with logo topbar, print & download buttons.
  - `letter_pdf.php` – generates PDF via TCPDF (or HTML fallback if TCPDF missing).
  - `letters_table.sql` – SQL schema for `letters` table.

## 🗄 Database

Example core tables (simplified):

- `users`
  - `id` (PK)
  - `username`
  - `password_hash`

- `category`
  - `id` (PK)
  - `user_id` (or global)
  - `name`
  - `type` (`income` or `expense`)

- `income`, `expense`, `debt`
  - `id` (PK)
  - `user_id`
  - `category_id`
  - `amount`
  - `date`
  - `note`

- `budget`
  - `id` (PK)
  - `user_id`
  - `category_id`
  - `limit_amount`

- `letters`
  - `id` (PK)
  - `user_id`
  - `recipient_name`
  - `recipient_address`
  - `subject`
  - `body`
  - `letter_date`
  - `created_at`



