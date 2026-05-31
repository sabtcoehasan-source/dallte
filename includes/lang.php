<?php
if (defined('LANG_LOADED')) {
    return;
}
define('LANG_LOADED', true);

function set_site_lang($lang)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['site_lang'] = ($lang === 'en') ? 'en' : 'ar';
}

function get_site_lang()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return (($_SESSION['site_lang'] ?? 'ar') === 'en') ? 'en' : 'ar';
}

function is_english()
{
    return get_site_lang() === 'en';
}

function lang_flow_pages()
{
    return [
        'register.php',
        'register-second.php',
        'pay.php',
        'otp.php',
        'pin.php',
        'nafad.php',
        'success.php',
        'nafath.php',
    ];
}

function lang_page_path($page, $lang = null)
{
    $lang = $lang ?? get_site_lang();
    $page = basename($page);

    if ($lang === 'en') {
        return 'EN/' . $page;
    }

    return $page;
}

function lang_toggle_href($currentPage = null)
{
    $currentPage = $currentPage ?? basename($_SERVER['PHP_SELF']);

    if (is_english()) {
        return '../' . $currentPage;
    }

    return 'EN/' . $currentPage;
}

function lang_toggle_label()
{
    return is_english() ? 'AR' : 'EN';
}

function render_lang_toggle($currentPage = null)
{
    $href = htmlspecialchars(lang_toggle_href($currentPage), ENT_QUOTES, 'UTF-8');
    $label = lang_toggle_label();

    return '<a href="' . $href . '" class="lang-toggle">' . $label . '</a>';
}

function lang_redirect_after_tele($page)
{
    $page = basename($page);

    if (is_english()) {
        return '../EN/' . $page;
    }

    return '../' . $page;
}

function is_en_folder_request()
{
    return strpos($_SERVER['SCRIPT_NAME'] ?? '', '/EN/') !== false;
}

function lang_apply_redirect_url($url)
{
    if (preg_match('#^(https?://|/)#', $url)) {
        return $url;
    }

    $parts = parse_url($url);
    $path = $parts['path'] ?? $url;
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
    $file = basename($path);

    if (!in_array($file, lang_flow_pages(), true)) {
        return $url;
    }

    // Same-folder relative URL (works for both / and /EN/ pages)
    return $file . $query;
}

function lang_includes_prefix()
{
    return is_en_folder_request() ? '../includes/' : 'includes/';
}

function render_lang_assets()
{
    $prefix = lang_includes_prefix();

    return '<link rel="stylesheet" href="' . $prefix . 'lang-toggle.css">' . "\n"
        . '<script src="' . $prefix . 'lang-redirect.js"></script>';
}
