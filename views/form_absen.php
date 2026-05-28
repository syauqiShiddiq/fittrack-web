<?php 
$theme = $_SESSION['theme_color'] ?? 'sky';
if (!in_array($theme, ['sky', 'emerald', 'amber', 'violet'])) $theme = 'sky';
?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Catat Aktivitas Olahragamu</h1>
        <p class="text-slate-500 mb-8 text-sm">Setiap menit berharga. Jangan lupa unggah bukti fotonya!</p>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 text-sm font-medium">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= $base ?>/absen" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Durasi Olahraga (Menit)</label>
                <input type="number" name="duration" min="1" max="1000" placeholder="Misal: 30" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-<?= $theme ?>-500 focus:ring-<?= $theme ?>-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Kegiatan</label>
                <textarea name="description" rows="3" placeholder="Misal: Lari sore di taman..." required 
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-<?= $theme ?>-500 focus:ring-<?= $theme ?>-500 outline-none transition"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Foto Bukti</label>
                <input type="file" name="photo" accept="image/jpeg, image/png, image/jpg" required 
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-600
                       file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold 
                       file:bg-<?= $theme ?>-50 file:text-<?= $theme ?>-700 hover:file:bg-<?= $theme ?>-100 transition">
                <p class="text-xs text-slate-400 mt-2">* Format didukung: JPG, JPEG, PNG (Maks 5MB)</p>
            </div>

            <button type="submit" class="w-full bg-<?= $theme ?>-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-sm hover:bg-<?= $theme ?>-700 transition transform hover:-translate-y-0.5">
                Simpan & Unggah Aktivitas
            </button>
        </form>
    </div>
</div>

<?php require 'partials/footer.php'; ?>