# Hangman

### Installation:

Update composer:
``` 
composer update 
```

Create database schema:
``` 
php app/console doctrine:database:create 
php app/console doctrine:schema:create 
```
Apply mysql dump `src/Lapikov/HangmanBundle/Resources/sql/words.english` to database.

Enjoy your Hangman game via /games API!
