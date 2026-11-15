# Vault Breaker — XAMPP/PHP project

## Install
1. Extract this folder to `C:\xampp\htdocs\vault_breaker`.
2. Start Apache and MySQL in XAMPP.
3. In phpMyAdmin, import `database/vault_breaker.sql`.
4. Visit `http://localhost/vault_breaker/register.php`, create an account, then log in.
5. Select ENTER THE VAULTS.

Default database settings are in `config/database.php` (localhost, root, blank password).

## Banana API
`api/puzzle.php` calls `https://marcconrad.com/uob/banana/api.php?out=json`. It uses PHP cURL if available and falls back to `file_get_contents` streams if cURL is unavailable. If streams are disabled, enable `allow_url_fopen` in the active php.ini. The answer is stored in the PHP session, not sent to the browser. Confirm current response fields with https://marcconrad.com/uob/banana/doc.php.

## Game
10 vault targets are presented; each vault requires three fresh API puzzle locks. Wrong answer +12 alert, passive alert +1 every 12 seconds, 60-second timer, timeout +8 alert. Random events: guard patrol freezes input for 3 seconds, firewall removes 10 seconds, power surge adds 10 seconds. XP/clearance and completion progress are stored in MySQL.

## Assignment themes
- Version control: Git commits and feature branches.
- Event-driven programming: DOM form events, timer intervals, random event handling and UI updates.
- Interoperability: PHP HTTP request to Banana API and JSON parsing.
- Virtual identity: registration, password hashing/verification, PHP sessions and session cookie.

## Git evidence
```bash
git init
git add .
git commit -m "Initial Vault Breaker PHP project"
git switch -c feature/banana-api
git add api/
git commit -m "Integrate Banana API"
git switch main
git switch -c feature/virtual-identity
git add register.php login.php logout.php includes/ config/
git commit -m "Implement agent identity and sessions"
git switch main
git switch -c feature/event-system
git add game.php assets/js/
git commit -m "Add event-driven game mechanics"
git switch main
git merge feature/banana-api
git merge feature/virtual-identity
git merge feature/event-system
```
If Git initialized with `master`, use `master` instead of `main`. This is a coursework prototype, not production-ready.
