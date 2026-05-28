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

    // Menyediakan data JSON untuk AJAX (Semua User)
    public function getWorkoutDays() {
        header('Content-Type: application/json');

        // Mengambil tanggal unik dan durasi olahraga dari seluruh user
        $stmt = $this->pdo->query("
            SELECT DATE(created_at) as date, COUNT(*) as total_workout, SUM(duration) as total_duration
            FROM workouts 
            GROUP BY DATE(created_at)
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
        exit;
    }

    // Menyediakan data detail olahraga spesifik per tanggal (JSON)
    public function getWorkoutDetails() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['date'])) {
            echo json_encode([]);
            exit;
        }

        $date = $_GET['date'];

        // Menarik data dari seluruh user beserta warna tema mereka
        $stmt = $this->pdo->prepare("
            SELECT w.description, w.duration, w.photo_url, u.username, u.theme_color, u.avatar_url 
            FROM workouts w 
            JOIN users u ON w.user_id = u.id 
            WHERE DATE(w.created_at) = ?
            ORDER BY w.created_at DESC
        ");
        $stmt->execute([$date]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($details);
        exit;
    }
}