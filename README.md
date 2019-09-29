# bet-api

## Installation

1. `git clone https://github.com/MantasPeldzius/bet-api.git` (add `.` if clone into current directory)
2. `composer install`
3. Create `.env` from `.env.example`
4. Create Database
5. Fill required configuration in `.env` for database connection (disable stric mode with `DB_STRICT_MODE=false`)
6. Create tables `php artisan migrate:fresh` (or use `bet-api.sql` in root folder)
7. Make sure that webservice can write in storage folder
8. `mod_rewrite` needed

## Usage

1. From index user can test some simple betslips with form
2. for submiting betslips = `POST */api/bet`
