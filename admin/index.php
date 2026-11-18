<?php
/**
 * LabVale Admin - Sub-router / Entry
 */
require_once __DIR__ . '/../includes/auth.php';

// If accessed directly without URL rewrite, dispatch based on query param or route
$route = $_GET['r'] ?? '';

switch ($route) {
    case 'login':
        require __DIR__ . '/login.php';
        break;
    case 'logout':
        require __DIR__ . '/logout.php';
        break;
    case 'projects':
        require __DIR__ . '/projects.php';
        break;
    case 'research-areas':
        require __DIR__ . '/research-areas.php';
        break;
    case 'articles':
        require __DIR__ . '/articles.php';
        break;
    case 'news':
        require __DIR__ . '/news.php';
        break;
    case 'contacts':
        require __DIR__ . '/contacts.php';
        break;
    case 'media':
        require __DIR__ . '/media.php';
        break;
    case 'settings':
        require __DIR__ . '/settings.php';
        break;
    default:
        require __DIR__ . '/dashboard.php';
        break;
}
