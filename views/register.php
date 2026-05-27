<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - FitTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 w-full max-w-md">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Buat Akun FitTrack</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="<?= $base_path ?>/register" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="register">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded-lg hover:bg-slate-900 transition font-medium">Daftar Sekarang</button>
        </form>
        <p class="text-sm text-center text-slate-500 mt-6">Sudah punya akun? <a href="<?= $base_path ?>/login" class="text-sky-600 hover:underline">Masuk</a></p>
    </div>
</body>
</html>