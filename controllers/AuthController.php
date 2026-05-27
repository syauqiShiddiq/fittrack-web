<?php
class AuthController {
    private $pdo;
    private $base_path = '/fittrack-web';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: " . $this->base_path . "/login");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Username atau Email sudah terdaftar.";
            header("Location: " . $this->base_path . "/register");
            exit;
        }
    }

    public function login($data) {
        $username = trim($data['username']);
        $password = $data['password'];

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: " . $this->base_path . "/dashboard");
            exit;
        } else {
            $_SESSION['error'] = "Username atau password salah.";
            header("Location: " . $this->base_path . "/login");
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header("Location: " . $this->base_path . "/login");
        exit;
    }
}