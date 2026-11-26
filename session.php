<?php
/**
 * Gestion de session et garde-fous d'authentification.
 * À inclure en tout premier dans les pages et les endpoints API.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Pour les endpoints API (api/*.php) : coupe la requête avec une erreur
 * JSON 401 si l'utilisateur n'est pas connecté. Retourne sinon l'id user.
 */
function require_login(): int
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Non authentifié.']);
        exit;
    }
    return (int) $_SESSION['user_id'];
}

/**
 * Pour les pages HTML (dashboard.php) : redirige vers login.php
 * si l'utilisateur n'est pas connecté.
 */
function require_login_page(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
