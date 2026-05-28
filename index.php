<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/WorkoutController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/CalendarController.php';
require_once __DIR__ . '/controllers/ProfileController.php';

$auth = new AuthController($pdo);
$workout = new WorkoutController($pdo);
$dashboard = new DashboardController($pdo);
$calendar = new CalendarController($pdo);
$profile = new ProfileController($pdo);
$base_path = '/fittrack-web'; // Sesuaikan dengan nama folder di Laragon
$route = str_replace($base_path, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Tangani pengiriman form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register') $auth->register($_POST);
    if ($_POST['action'] === 'login') $auth->login($_POST);
    if ($_POST['action'] === 'submit_absen') $workout->store($_POST, $_FILES);
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
        $dashboard->index();
        break;
    case '/absen':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        require __DIR__ . '/views/form_absen.php';
        break;
    case '/kalender':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        $calendar->index();
        break;
    case '/api/workout-days':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        $calendar->getWorkoutDays();
        break;
    case '/api/workout-details':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        $calendar->getWorkoutDetails();
        break;
    case '/profil':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        $profile->index();
        break;
    case '/profil/update':
        if(!isset($_SESSION['user_id'])) { header("Location: $base_path/login"); exit; }
        $profile->update();
        break;
    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}