<?php
require_once __DIR__ . '/../vendor/autoload.php';

class GDriveService {
    private $service;
    // TODO: Ganti string di bawah ini dengan Folder ID Google Drive milikmu
    private $folderId = 'PASTE_FOLDER_ID_DISINI'; 

    public function __construct() {
        $client = new Google\Client();
        
        // Mengarah ke file JSON service account yang sudah diunduh sebelumnya
        $client->setAuthConfig(__DIR__ . '/gdrive_auth.json');
        $client->addScope(Google\Service\Drive::DRIVE_FILE);
        
        $this->service = new Google\Service\Drive($client);
    }

    /**
     * Fungsi untuk mengunggah file ke folder spesifik di Google Drive
     * * @param string $filePath Jalur file sementara (tmp_name)
     * @param string $fileName Nama asli file atau nama acak baru
     * @param string $mimeType Tipe file (image/jpeg, image/png, dll)
     * @return array|false Mengembalikan array berisi ID dan Link jika sukses, false jika gagal
     */
    public function uploadFile($filePath, $fileName, $mimeType) {
        $fileMetadata = new Google\Service\Drive\DriveFile([
            'name' => $fileName,
            'parents' => [$this->folderId]
        ]);

        try {
            $content = file_get_contents($filePath);
            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, webViewLink'
            ]);
            
            return [
                'id' => $file->id,
                'link' => $file->webViewLink
            ];
        } catch (Exception $e) {
            // Bisa menambahkan log error di sini jika diperlukan untuk debugging
            return false;
        }
    }
}