<?php
class CalendarController {
    private $pdo;
    private $base_path = '/fittrack-web';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Menampilkan halaman utama kalender
    public function index() {
        require __DIR__ . '/../views/kalender.php';
    }

    // Menyediakan data JSON untuk AJAX
    public function getWorkoutDays() {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];

        // Mengambil tanggal unik dan durasi olahraga milik user yang sedang login
        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as total_workout, SUM(duration) as total_duration
            FROM workouts 
            WHERE user_id = ? 
            GROUP BY DATE(created_at)
        ");
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
        exit;
    }
}