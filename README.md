# Tic-Tac-Toe Game — PHP + MySQL

A web-based Tic-Tac-Toe game played against an unbeatable AI (Minimax), built with
**PHP, MySQL, HTML, CSS and JavaScript**. Designed as a DBMS mini-project.

## Features

- **User Registration & Login** — secure auth with hashed passwords (`password_hash` / `password_verify`) and PHP sessions
- **Play vs AI** — client-side Minimax with alpha-beta pruning (never loses)
- **Persistent Statistics** — aggregate win/loss/draw stats stored in MySQL
- **Match History** — every game recorded in a `match_history` table
- **Additional Feature — Search / Filter** — filter history by result (Win/Loss/Draw) and search by date on the stats page
- **Additional Feature — Update** — logged-in users can edit their username and change their password from the Profile page

## Tech Stack

- PHP 8.x (PDO with prepared statements)
- MySQL (see `database.sql`)
- HTML5 / CSS3
- Vanilla JavaScript (Minimax AI)

## Security

- All queries use **prepared statements** (PDO) — no SQL injection
- All output escaped with `htmlspecialchars()` — no XSS
- Passwords stored as **bcrypt** hashes
- Server-side validation on every form

## Setup (Local / XAMPP)

1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy the project folder to `C:\xampp\htdocs\tic_tac_toe_php`.
3. Open phpMyAdmin and import `database.sql` (creates `tictactoe_db`).
4. Update the database credentials in `config.php` (XAMPP default: host `localhost`, user `root`, empty password).
5. Open `http://localhost/tic_tac_toe_php` in your browser.
6. Register an account and start playing.

Sample accounts (from `database.sql`): `alice` / `password123`, `bob` / `password123`.

## Setup (InfinityFree / Shared Hosting)

1. Log in to your InfinityFree control panel and create a MySQL **database + user**.
   Note the **DB host** (e.g. `sqlXXX.infinityfree.com`) — it is *not* `localhost`.
2. Open **phpMyAdmin**, select your database, **Import** → upload `database.sql`.
3. In **File Manager** → `htdocs`, delete the default files and upload all files in this repo.
4. Edit `config.php` and fill in the matching `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
5. Visit your assigned InfinityFree URL.
6. Test in an incognito/private window before submitting.

## File Structure

```
tic_tac_toe_php/
├── index.php        # Landing page
├── register.php     # Sign up
├── login.php        # Sign in
├── logout.php       # Sign out
├── game.php         # Main game page (board + AI)
├── game.js          # Frontend logic + Minimax AI
├── stats.php        # Stats + match history (search/filter)
├── profile.php      # Update username / password
├── api.php          # AJAX endpoint to save match results
├── config.php       # DB connection (PDO)
├── database.sql     # MySQL schema + sample data
├── style.css        # Styling
└── screenshots/     # Evidence screenshots
```

## Database Schema (`tictactoe_db`)

- **users** — `id`, `username`, `password`, `created_at`
- **game_stats** — `user_id` (FK), `games_played`, `player_wins`, `ai_wins`, `draws`, `last_played_at`
- **match_history** — `id`, `user_id` (FK, cascade delete), `result` (win/loss/draw), `played_at`

## How to Play

1. Register or log in.
2. Open the **Play** tab — you are **X**, the AI is **O**.
3. Click a cell to place your move; the AI replies instantly.
4. See your results in **Stats** and edit your account in **Profile**.
5. Logout switches between players.