<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): bool
{
    $submittedToken = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $submittedToken);
}

function require_auth(?string $requiredRole = null): void
{
    if (!isset($_SESSION['username']) || trim((string) $_SESSION['username']) === '') {
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }

    if ($requiredRole !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole)) {
        header('Location: index.php');
        exit;
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function verify_password(string $password, string $storedHash): bool
{
    if (password_verify($password, $storedHash)) {
        return true;
    }

    return hash_equals(md5($password), $storedHash);
}
