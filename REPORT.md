# DBMS Mini Project — Report

## Tic-Tac-Toe Game using PHP & MySQL

---

### College / Institute Details
- **College Name:** ______________________
- **Department:** ______________________

### Student Details
- **Roll Number:** ______________________
- **Name:** ______________________
- **Subject:** DBMS — Mini Project

---

### 1. Introduction
Tic-Tac-Toe is a classic two-player paper-and-pencil game. This project converts the
game into a web application where a single player competes against a computer AI
rather than another human. The AI is powered by the **Minimax algorithm with
alpha-beta pruning**, which guarantees the AI never loses a game.

The application is built using **PHP** for server-side logic, **MySQL** for persistent
data storage, and **HTML, CSS, and JavaScript** for the user interface.

### 2. Problem Statement
**Roll Number MLU24F154** hereby declares that the mini project titled
**"Tic-Tac-Toe Game using PHP & MySQL"** is selected for the DBMS Mini Project
component of the 2025–2026 academic year, subject to evaluation by the Department.

**Selected Problem Statement Number:** (fill from faculty list)

### 3. Objectives
- Provide an interactive web-based Tic-Tac-Toe game.
- Maintain user accounts (register / login).
- Store and display persistent game statistics.
- Demonstrate DBMS concepts: tables, foreign keys, joins, prepared statements.

### 4. Additional Features Implemented
1. **Search / Filter** — filter match history by result (Win/Loss/Draw) and search by date.
2. **Update** — users can edit their username and change their password.

> More than one additional feature has been implemented.

### 5. Technologies Used
| Layer      | Technology          |
|------------|---------------------|
| Frontend   | HTML5, CSS3, JavaScript |
| Backend    | PHP 8.x             |
| Database   | MySQL               |
| Web Server | Apache / InfinityFree |

### 6. Database Design
Three tables are created by `database.sql`:

- **users** — `id (PK)`, `username (unique)`, `password (hash)`, `created_at`
- **game_stats** — `user_id (FK→users.id)`, `games_played`, `player_wins`, `ai_wins`, `draws`, `last_played_at`
- **match_history** — `id (PK)`, `user_id (FK→users.id, ON DELETE CASCADE)`, `result (win/loss/draw)`, `played_at`

Relations:
- `users 1 ─ 1 game_stats`
- `users 1 ─ N match_history`

### 7. Features in Detail
**Authentication** — Registration validates the username format and password strength,
hashes the password with `password_hash()`, and stores only the hash. Login verifies
credentials with `password_verify()` and starts a PHP session.

**Game Play** — The board is rendered in the browser and `game.js` implements the
Minimax algorithm. After every move the AI computes the best response in `O(9!)`
worst-case, with alpha-beta pruning cutting branches that cannot affect the outcome.

**Match Saving** — When a game ends, `api.php` saves the result inside a transaction:
it increments the aggregate values in `game_stats` and inserts a row into
`match_history`.

**Stats Page** — Displays aggregate cards (games played, wins, losses, draws, win rate)
and a match-history table filterable by result and searchable by date.

**Profile Update** — The `profile.php` page lets a logged-in user update their
username and change their password. Username uniqueness is enforced with a tri-state
query (`username = ? AND id != ?`), and an empty password field keeps the current one.

### 8. Security Considerations
- **SQL Injection safe** — 100% prepared statements via PDO.
- **XSS safe** — every dynamic value is escaped with `htmlspecialchars()`.
- **Password security** — bcrypt hashing via PHP's `password_hash()`.
- **Server-side validation** — all inputs are validated before hitting the database.

### 9. Screenshots
Screenshots are provided in the `screenshots/` folder:

1. `image1.png` — Landing / registration page
2. `image2.png` — Login page
3. `image3.png` — Game board (playing vs AI)
4. `image4.png` — Game over result
5. `image5.png` — Stats / match history with filter

### 10. Result
The project fulfills the mini-project requirements:
- Working web interface hosted on **InfinityFree**
- Database-driven records for users, stats and match history
- More than one additional feature (Search/Filter, Update)

### 11. Conclusion
The Tic-Tac-Toe web game demonstrates a complete two-tier web application backed by a
relational database. It applies core DBMS concepts — table design, foreign keys,
transactions, and prepared statements — together with a well-organized PHP back-end
and an interactive JavaScript front-end.

---
*Report generated for the DBMS Mini Project submission.*