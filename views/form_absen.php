<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Absen Olahraga Hari Ini</h2>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="<?= $base ?>/absen" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="action" value="submit_absen">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Kegiatan</label>
                <textarea name="description" rows="3" required placeholder="Contoh: Lari keliling komplek 3 putaran, dilanjut push up 20x..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Durasi (Menit)</label>
                <input type="number" name="duration" min="1" required placeholder="Contoh: 45" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Bukti Olahraga</label>
                <input type="file" name="photo" accept="image/*" required class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
            </div>

            <button type="submit" class="w-full bg-sky-600 text-white py-3 rounded-lg hover:bg-sky-700 transition font-medium flex justify-center items-center">
                Simpan Absensi
            </button>
        </form>
    </div>
</div>

<?php require 'partials/footer.php'; ?>