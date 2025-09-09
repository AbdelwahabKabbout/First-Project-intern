@php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}

if (isset($_GET['theme'])) {
$theme = $_GET['theme'] === 'dark' ? 'dark' : 'light';
$_SESSION['theme'] = $theme;

$currentUrl = strtok($_SERVER["REQUEST_URI"], '?');
$queryParams = $_GET;
unset($queryParams['theme']);

if (!empty($queryParams)) {
$currentUrl .= '?' . http_build_query($queryParams);
}

header("Location: $currentUrl");
exit();
}

$currentTheme = $_SESSION['theme'] ?? 'light';
$cssFile = $currentTheme === 'dark' ? 'Dark.css' : 'Light.css';

if (!function_exists('addThemeToUrl')) {
function addThemeToUrl($url) {
$currentTheme = $_SESSION['theme'] ?? 'light';
if ($currentTheme === 'dark') {
$separator = strpos($url, '?') !== false ? '&' : '?';
return $url . $separator . 'theme=dark';
}
return $url;
}
}

if (!function_exists('getThemeHiddenInput')) {
function getThemeHiddenInput() {
$currentTheme = $_SESSION['theme'] ?? 'light';
if ($currentTheme === 'dark') {
return '<input type="hidden" name="theme" value="dark">';
}
return '';
}
}

$existingParams = $_GET;
unset($existingParams['theme']);
$queryString = !empty($existingParams) ? '&' . http_build_query($existingParams) : '';

config(['theme.current' => $currentTheme]);
config(['theme.cssFile' => $cssFile]);
@endphp

<link rel="stylesheet" href="{{ asset($cssFile) }}">

<div class="theme-toggle-container">
    @if ($currentTheme === 'light')
    <a href="?theme=dark{{ $queryString }}" class="theme-toggle-btn">
        <span class="theme-text">Dark Mode</span>
    </a>
    @else
    <a href="?theme=light{{ $queryString }}" class="theme-toggle-btn">
        <span class="theme-text">Light Mode</span>
    </a>
    @endif
</div>