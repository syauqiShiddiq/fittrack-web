<?php
class DashboardController {
    private $pdo;
    private $base_path = '/fittrack-web';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        // Mengambil 10 riwayat olahraga terbaru dari semua user
        $stmt = $this->pdo->query("
            SELECT w.description, w.duration, w.photo_url, w.created_at, u.username, u.avatar_url 
            FROM workouts w 
            JOIN users u ON w.user_id = u.id 
            ORDER BY w.created_at DESC 
            LIMIT 10
        ");
        $recent_workouts = $stmt->fetchAll();

        require __DIR__ . '/../views/dashboard.php';
    }
}