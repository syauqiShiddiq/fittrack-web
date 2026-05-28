<?php 
// --- BLOK LOGIKA PENGAMBILAN DATA STATISTIK ---
global $pdo;
$userId = $_SESSION['user_id'];

// 1. Ambil Data Preferensi Personal User
$stmtUser = $pdo->prepare("SELECT theme_color, weekly_target, motivation_quote FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

$theme = $userData['theme_color'] ?: 'sky';
if (!in_array($theme, ['sky', 'emerald', 'amber', 'violet'])) $theme = 'sky';
$weeklyTarget = $userData['weekly_target'] ?: 150;
$motivation = $userData['motivation_quote'] ?: "Disiplin adalah jembatan antara tujuan dan pencapaian!";

// 2. Hitung Pencapaian Personal (Minggu Ini)
$stmtMyWeek = $pdo->prepare("SELECT SUM(duration) as my_total FROM workouts WHERE user_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)");
$stmtMyWeek->execute([$userId]);
$myWeeklyTotal = $stmtMyWeek->fetch(PDO::FETCH_ASSOC)['my_total'] ?? 0;
$progressPercent = min(100, round(($myWeeklyTotal / $weeklyTarget) * 100));

// 3. Ambil Total Durasi Grup (Bulan Ini)
$stmtGroup = $pdo->query("SELECT SUM(duration) as total_month FROM workouts WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$totalGroupMonth = $stmtGroup->fetch(PDO::FETCH_ASSOC)['total_month'] ?? 0;

// 4. Ambil Leaderboard Mingguan
$stmtLeaderboard = $pdo->query("
    SELECT u.username, u.avatar_url, SUM(w.duration) as total_duration 
    FROM workouts w JOIN users u ON w.user_id = u.id 
    WHERE YEARWEEK(w.created_at, 1) = YEARWEEK(CURRENT_DATE(), 1) 
    GROUP BY u.id ORDER BY total_duration DESC LIMIT 5
");
$leaderboard = $stmtLeaderboard->fetchAll(PDO::FETCH_ASSOC);

// 5. Persiapkan Data Grafik Chart.js (7 Hari Terakhir)
$daysId = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
$chartLabelsRaw = []; $chartLabelsDisplay = [];
for ($i = 6; $i >= 0; $i--) {
    $dateStr = date('Y-m-d', strtotime("-$i days"));
    $chartLabelsRaw[] = $dateStr;
    $chartLabelsDisplay[] = $daysId[date('D', strtotime($dateStr))];
}

$stmtChart = $pdo->query("
    SELECT u.username, DATE(w.created_at) as tgl, SUM(w.duration) as durasi 
    FROM workouts w JOIN users u ON w.user_id = u.id 
    WHERE w.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) 
    GROUP BY u.id, DATE(w.created_at)
");
$chartDataRaw = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

$usersData = [];
foreach($chartDataRaw as $row) { $usersData[$row['username']][$row['tgl']] = $row['durasi']; }

$datasets = [];
$colors = ['#0284c7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
$colorIndex = 0;
foreach($usersData as $username => $dataTgl) {
    $dataArray = [];
    foreach($chartLabelsRaw as $label) { $dataArray[] = $dataTgl[$label] ?? 0; }
    $datasets[] = [
        'label' => $username,
        'data' => $dataArray,
        'backgroundColor' => $colors[$colorIndex % count($colors)],
        'borderRadius' => 4
    ];
    $colorIndex++;
}
$chartConfig = ['labels' => $chartLabelsDisplay, 'datasets' => $datasets];
// --- AKHIR BLOK LOGIKA ---
?>

<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8 flex flex-col lg:flex-row justify-between items-center gap-6">
        <div class="w-full lg:w-1/2">
            <h1 class="text-2xl font-bold text-slate-800">Halo, <?= htmlspecialchars($_SESSION['username']) ?>! 👋</h1>
            <p class="text-<?= $theme ?>-600 font-medium italic mt-2 text-lg">"<?= htmlspecialchars($motivation) ?>"</p>
        </div>
        
        <div class="w-full lg:w-1/2 bg-slate-50 border border-slate-100 rounded-xl p-4">
            <div class="flex justify-between items-end mb-2">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target Mingguanmu</p>
                    <p class="text-lg font-extrabold text-slate-800"><?= $myWeeklyTotal ?> <span class="text-sm font-medium text-slate-500">/ <?= $weeklyTarget ?> Menit</span></p>
                </div>
                <div class="text-<?= $theme ?>-600 font-bold text-lg"><?= $progressPercent ?>%</div>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3 mb-1 overflow-hidden">
                <div class="bg-<?= $theme ?>-500 h-3 rounded-full transition-all duration-1000" style="width: <?= $progressPercent ?>%"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <a href="<?= $base ?>/absen" class="w-full md:w-auto text-center bg-<?= $theme ?>-600 text-white px-6 py-3.5 rounded-xl text-sm font-bold shadow-sm hover:bg-<?= $theme ?>-700 transition transform hover:-translate-y-0.5">
            + Catat Olahraga Hari Ini
        </a>
        <div class="bg-slate-800 border border-slate-700 px-5 py-2.5 rounded-xl text-center w-full md:w-auto shadow-sm">
            <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">🔥 Total Grup Bulan Ini</p>
            <p class="text-xl font-extrabold text-white"><?= $totalGroupMonth ?> <span class="text-sm font-medium text-slate-400">Menit</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Statistik 7 Hari Terakhir</h2>
            <div class="relative h-64 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🏆 Leaderboard Minggu Ini</h2>
            <div class="space-y-4">
                <?php if(empty($leaderboard)): ?>
                    <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas minggu ini.</p>
                <?php else: ?>
                    <?php foreach($leaderboard as $index => $user): ?>
                        <div class="flex items-center justify-between p-3 rounded-xl <?= $index === 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100' ?>">
                            <?php
                                $lboardAvatar = '';
                                if (!empty($user['avatar_url']) && preg_match('/d\/([a-zA-Z0-9_-]+)/', $user['avatar_url'], $matches)) {
                                    $lboardAvatar = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w100";
                                }
                            ?>
                            <div class="flex items-center gap-3">
                                <?php if ($lboardAvatar): ?>
                                    <img src="<?= htmlspecialchars($lboardAvatar) ?>" class="w-8 h-8 rounded-full object-cover border <?= $index === 0 ? 'border-amber-400 shadow-md ring-2 ring-amber-100' : 'border-slate-200' ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm <?= $index === 0 ? 'bg-amber-400 text-white shadow-md' : 'bg-slate-200 text-slate-600' ?>">
                                        <?= $index + 1 ?>
                                    </div>
                                <?php endif; ?>
                                <p class="font-bold text-sm text-slate-800"><?= htmlspecialchars($user['username']) ?></p>
                            </div>
                            <div class="text-sm font-bold <?= $index === 0 ? 'text-amber-600' : 'text-slate-600' ?>">
                                <?= $user['total_duration'] ?>m
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-10">
        <h2 class="font-bold text-slate-800 mb-6 border-b border-slate-100 pb-2">Aktivitas Terbaru</h2>
        <div class="space-y-6">
            <?php if(empty($recent_workouts)): ?>
                <div class="text-center text-slate-500 py-8">Belum ada aktivitas olahraga.</div>
            <?php else: ?>
                <?php foreach($recent_workouts as $workout): ?>
                    <div class="flex flex-col sm:flex-row gap-5 border-b border-slate-100 pb-6 last:border-0 last:pb-0">
                        <div class="sm:w-1/4 flex-shrink-0">
                            <?php
                                $directUrl = $workout['photo_url'];
                                if (preg_match('/d\/([a-zA-Z0-9_-]+)/', $workout['photo_url'], $matches)) {
                                    $directUrl = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w1000";
                                }
                            ?>
                            <img src="<?= htmlspecialchars($directUrl) ?>" alt="Bukti" class="w-full h-32 object-cover rounded-xl border border-slate-200">
                        </div>
                        
                        <div class="flex items-center gap-2 mb-2">
                            <?php
                                $feedAvatar = '';
                                // Memastikan query untuk recent_workouts (di controller dashboard-mu) sudah memanggil u.avatar_url
                                if (!empty($workout['avatar_url']) && preg_match('/d\/([a-zA-Z0-9_-]+)/', $workout['avatar_url'], $matches)) {
                                    $feedAvatar = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w100";
                                }
                            ?>
                            <?php if ($feedAvatar): ?>
                                <img src="<?= htmlspecialchars($feedAvatar) ?>" class="w-7 h-7 rounded-full object-cover border border-<?= $theme ?>-200 shadow-sm" alt="Avatar">
                            <?php else: ?>
                                <div class="w-7 h-7 bg-<?= $theme ?>-100 text-<?= $theme ?>-600 rounded-full flex items-center justify-center font-bold text-xs border border-<?= $theme ?>-200">
                                    <?= strtoupper(substr($workout['username'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($workout['username']) ?></h3>
                            <span class="text-slate-300">•</span>
                            <p class="text-xs text-slate-400"><?= date('d M - H:i', strtotime($workout['created_at'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chartData = <?= json_encode($chartConfig) ?>;
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Menit' } } }
        }
    });
});
</script>

<?php require 'partials/footer.php'; ?>