<?php
// Panggil file konfigurasi GDrive di bagian paling atas
require_once __DIR__ . '/../config/gdrive_service.php';

class ProfileController {
    private $pdo;
    private $gdrive;
    private $base_path = '/fittrack-web';

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Inisialisasi class GDriveService sama seperti di WorkoutController
        $this->gdrive = new GDriveService(); 
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/profil.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $motivationQuote = trim($_POST['motivation_quote']);
            $weeklyTarget = (int) $_POST['weekly_target'];
            $themeColor = $_POST['theme_color'];
            
            $avatarUrl = null;

            // Cek apakah ada file avatar baru yang diunggah
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                
                // Ambil data file (persis seperti logika WorkoutController)
                $tmpName = $_FILES['avatar']['tmp_name'];
                $fileName = time() . '_avatar_' . basename($_FILES['avatar']['name']);
                $mimeType = $_FILES['avatar']['type'];

                // Eksekusi fungsi upload dari Helper GDrive
                $uploadResult = $this->gdrive->uploadFile($tmpName, $fileName, $mimeType);
                
                // Jika berhasil, ambil tautannya (link)
                if ($uploadResult) {
                    $avatarUrl = $uploadResult['link'];
                }
            }

            // Siapkan Query SQL
            if ($avatarUrl) {
                // Jika upload avatar berhasil, update kolom avatar_url juga
                $stmt = $this->pdo->prepare("UPDATE users SET motivation_quote = ?, weekly_target = ?, theme_color = ?, avatar_url = ? WHERE id = ?");
                $executeStatus = $stmt->execute([$motivationQuote, $weeklyTarget, $themeColor, $avatarUrl, $userId]);
                if($executeStatus) $_SESSION['avatar_url'] = $avatarUrl; // Simpan di session untuk navbar
            } else {
                // Jika tidak ada avatar yang diunggah, update data teks saja
                $stmt = $this->pdo->prepare("UPDATE users SET motivation_quote = ?, weekly_target = ?, theme_color = ? WHERE id = ?");
                $executeStatus = $stmt->execute([$motivationQuote, $weeklyTarget, $themeColor, $userId]);
            }
            
            if ($executeStatus) {
                $_SESSION['theme_color'] = $themeColor;
                $_SESSION['success'] = "Profil berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Terjadi kesalahan sistem saat memperbarui profil.";
            }
            
            header("Location: " . $this->base_path . "/profil");
            exit;
        }
    }
}