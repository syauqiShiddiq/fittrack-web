<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);
$base_path = '/fittrack-web'; // Sesuaikan dengan nama folder di Laragon
$route = str_replace($base_path, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Tangani pengiriman form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register') $auth->register($_POST);
    if ($_POST['action'] === 'login') $auth->login($_POST);
}

// Tangani rute halaman (GET)
switch ($route) {
    case '':
    case '/':
    case '/login':
        if(isset($_SESSION['user_id'])) { header("Location: $base_path/dashboard"); exit; }
        require __DIR__ . '/views/login.php';
        break;
    case '/register':
        if(isset($_SESSION['user_id'])) { header("Location: $base_path/dashboard"); exit; }
        require __DIR__ . '/views/register.php';
        break;
    case '/logout':
        $auth->logout();
        break;
    case '/dashboard':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        // Kita buat file kosong sementara agar tidak error saat berhasil login
        if(!file_exists(__DIR__ . '/views/dashboard.php')) { file_put_contents(__DIR__ . '/views/dashboard.php', '<h1>Ini Dashboard</h1><a href="'.$base_path.'/logout">Logout</a>'); }
        require __DIR__ . '/views/dashboard.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}