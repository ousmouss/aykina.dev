<?php
/**
 * Connexion à la base de données MySQL via PDO.
 * Modifie ces constantes selon ta config locale (XAMPP/WAMP/MAMP/serveur).
 */

$DB_HOST = 'localhost';
$DB_NAME = 'tasks';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Connexion à la base de données impossible : ' . $e->getMessage()]));
}
