<?php 
$theme = $_SESSION['theme_color'] ?? 'sky';
if (!in_array($theme, ['sky', 'emerald', 'amber', 'violet'])) $theme = 'sky';
?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="bg-slate-50 border-b border-slate-100 p-6 text-center">
            <?php
                $avatarSrc = '';
                if (!empty($user['avatar_url'])) {
                    // Konversi URL GDrive ke format thumbnail resolusi 200px
                    if (preg_match('/d\/([a-zA-Z0-9_-]+)/', $user['avatar_url'], $matches)) {
                        $avatarSrc = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w200";
                    }
                }
            ?>

            <?php if ($avatarSrc): ?>
                <img src="<?= htmlspecialchars($avatarSrc) ?>" alt="Avatar" class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-<?= $theme ?>-200 shadow-sm mb-3">
            <?php else: ?>
                <div class="w-24 h-24 bg-<?= $theme ?>-100 text-<?= $theme ?>-600 rounded-full flex items-center justify-center font-bold text-4xl mx-auto shadow-sm mb-3 border-2 border-<?= $theme ?>-200">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h2 class="text-xl font-bold text-slate-800">@<?= htmlspecialchars($user['username']) ?></h2>
            <p class="text-slate-500 text-sm"><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <form action="<?= $base ?>/profil/update" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm font-medium">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-medium">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Ganti Foto Profil (Opsional)</label>
                <input type="file" name="avatar" accept="image/jpeg, image/png, image/jpg" 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-600
                       file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold 
                       file:bg-<?= $theme ?>-50 file:text-<?= $theme ?>-700 hover:file:bg-<?= $theme ?>-100 transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Target Olahraga Mingguan (Menit)</label>
                <input type="number" name="weekly_target" value="<?= htmlspecialchars($user['weekly_target']) ?>" min="10" max="1000" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-<?= $theme ?>-500 focus:ring-<?= $theme ?>-500 outline-none transition" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kata Motivasi (Mantra Pribadi)</label>
                <input type="text" name="motivation_quote" value="<?= htmlspecialchars($user['motivation_quote'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-<?= $theme ?>-500 focus:ring-<?= $theme ?>-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Warna Aksen Profil</label>
                <div class="flex gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_color" value="sky" class="peer sr-only" <?= ($user['theme_color'] == 'sky' || empty($user['theme_color'])) ? 'checked' : '' ?>>
                        <div class="w-10 h-10 rounded-full bg-sky-500 ring-4 ring-transparent peer-checked:ring-sky-300 peer-checked:scale-110 transition shadow-sm"></div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_color" value="emerald" class="peer sr-only" <?= $user['theme_color'] == 'emerald' ? 'checked' : '' ?>>
                        <div class="w-10 h-10 rounded-full bg-emerald-500 ring-4 ring-transparent peer-checked:ring-emerald-300 peer-checked:scale-110 transition shadow-sm"></div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_color" value="amber" class="peer sr-only" <?= $user['theme_color'] == 'amber' ? 'checked' : '' ?>>
                        <div class="w-10 h-10 rounded-full bg-amber-500 ring-4 ring-transparent peer-checked:ring-amber-300 peer-checked:scale-110 transition shadow-sm"></div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_color" value="violet" class="peer sr-only" <?= $user['theme_color'] == 'violet' ? 'checked' : '' ?>>
                        <div class="w-10 h-10 rounded-full bg-violet-500 ring-4 ring-transparent peer-checked:ring-violet-300 peer-checked:scale-110 transition shadow-sm"></div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-slate-900 transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<?php require 'partials/footer.php'; ?>