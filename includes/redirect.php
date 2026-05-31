<?php
if (defined('REDIRECT_LOADED')) return;
define('REDIRECT_LOADED', true);

require_once __DIR__ . '/lang.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($User) || !is_object($User)) return;

$userId = $_SESSION['current_user_id'] ?? null;
if (!$userId) return;

$redirect = $User->getRedirectUrl($userId);

if ($redirect) {

    // 🧹 امسح التوجيه فورًا (مرة واحدة فقط)
    $User->clearRedirect($userId);

    // 🚀 نفّذ التحويل
    header("Location: " . lang_apply_redirect_url($redirect));
    exit;
}

