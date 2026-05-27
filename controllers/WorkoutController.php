<?php
require_once __DIR__ . '/../config/gdrive_service.php';

class WorkoutController {
    private $pdo;
    private $gdrive;
    private $base_path = '/fittrack-web';

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->gdrive = new GDriveService();
    }

    public function store($data, $file) {
        $userId = $_SESSION['user_id'];
        $description = htmlspecialchars(trim($data['description']));
        $duration = (int)$data['duration'];

        // Cek jika ada file yang diunggah tanpa error
        if (isset($file['photo']) && $file['photo']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['photo']['tmp_name'];
            $fileName = time() . '_' . basename($file['photo']['name']);
            $mimeType = $file['photo']['type'];

            // Eksekusi fungsi upload dari Helper GDrive kita
            $uploadResult = $this->gdrive->uploadFile($tmpName, $fileName, $mimeType);

            if ($uploadResult) {
                $photoUrl = $uploadResult['link'];

                try {
                    $stmt = $this->pdo->prepare("INSERT INTO workouts (user_id, description, duration, photo_url) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$userId, $description, $duration, $photoUrl]);

                    $_SESSION['success'] = "Berhasil absen! Olahraga telah tercatat.";
                    header("Location: " . $this->base_path . "/dashboard");
                    exit;
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Gagal menyimpan data ke database.";
                }
            } else {
                $_SESSION['error'] = "Gagal mengunggah foto ke Google Drive. Cek Folder ID atau koneksi.";
            }
        } else {
            $_SESSION['error'] = "Harap sertakan foto bukti olahraga yang valid.";
        }

        // Jika gagal, kembalikan ke halaman form
        header("Location: " . $this->base_path . "/absen");
        exit;
    }
}