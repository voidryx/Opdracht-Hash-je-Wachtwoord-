<?php 
function verbindMetDatabase()
{
    $db = new PDO('sqlite:' . __DIR__ . '/gebruikers.db');
 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    $db->exec("
        CREATE TABLE IF NOT EXISTS gebruikers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            wachtwoord_hash TEXT NOT NULL
        )
    ");
 
    return $db;
}
 