<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Aktivitas Terbaru</h1>
        <a href="<?= $base ?>/absen" class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
            + Absen Sekarang
        </a>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 shadow-sm">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php if(empty($recent_workouts)): ?>
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 text-center text-slate-500">
                Belum ada aktivitas olahraga. Jadilah yang pertama absen hari ini!
            </div>
        <?php else: ?>
            <?php foreach($recent_workouts as $workout): ?>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row gap-5">
                    <div class="sm:w-1/3 flex-shrink-0">
                        <?php
                            // Mengekstrak ID file dan menggunakan jalur Thumbnail Google
                            $directUrl = $workout['photo_url'];
                            if (preg_match('/d\/([a-zA-Z0-9_-]+)/', $workout['photo_url'], $matches)) {
                                // parameter sz=w1000 memaksa Google memuat gambar dalam kualitas tinggi
                                $directUrl = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w1000";
                            }
                        ?>
                        <img src="<?= htmlspecialchars($directUrl) ?>" alt="Bukti Olahraga" class="w-full h-48 object-cover rounded-lg border border-slate-100">
                    </div>
                    
                    <div class="sm:w-2/3 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center font-bold text-sm">
                                <?= strtoupper(substr($workout['username'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800"><?= htmlspecialchars($workout['username']) ?></h3>
                                <p class="text-xs text-slate-500"><?= date('d M Y - H:i', strtotime($workout['created_at'])) ?></p>
                            </div>
                        </div>
                        
                        <div class="inline-block bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded mb-3 w-max font-medium">
                            ⏱ <?= $workout['duration'] ?> Menit
                        </div>
                        
                        <p class="text-slate-700 text-sm leading-relaxed">
                            <?= nl2br(htmlspecialchars($workout['description'])) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require 'partials/footer.php'; ?>