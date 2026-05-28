<?php 
$base = '/fittrack-web'; 
$username = $_SESSION['username'] ?? 'User';
$initial = strtoupper(substr($username, 0, 1));

// Mengambil warna tema dari session (default: sky)
$theme = $_SESSION['theme_color'] ?? 'sky'; 
if (!in_array($theme, ['sky', 'emerald', 'amber', 'violet'])) {
    $theme = 'sky';
}

// Logika Tautan Avatar Navbar
$avatarUrl = $_SESSION['avatar_url'] ?? null;
$navAvatarSrc = '';
if (!empty($avatarUrl) && preg_match('/d\/([a-zA-Z0-9_-]+)/', $avatarUrl, $matches)) {
    $navAvatarSrc = "https://drive.google.com/thumbnail?id=" . $matches[1] . "&sz=w100";
}
?>
<nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex items-center">
                <a href="<?= $base ?>/dashboard" class="text-xl font-bold text-<?= $theme ?>-600 tracking-tight">FitTrack</a>
            </div>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="<?= $base ?>/dashboard" class="text-slate-600 hover:text-<?= $theme ?>-600 font-medium transition text-sm">Dashboard</a>
                <a href="<?= $base ?>/absen" class="text-slate-600 hover:text-<?= $theme ?>-600 font-medium transition text-sm">Absen</a>
                <a href="<?= $base ?>/kalender" class="text-slate-600 hover:text-<?= $theme ?>-600 font-medium transition text-sm">Kalender</a>
                
                <div class="flex items-center gap-4 border-l border-slate-200 pl-6 ml-2">
                    <a href="<?= $base ?>/profil" class="flex items-center gap-2 group">
                        
                        <?php if ($navAvatarSrc): ?>
                            <img src="<?= htmlspecialchars($navAvatarSrc) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-<?= $theme ?>-200 group-hover:border-<?= $theme ?>-500 transition shadow-sm">
                        <?php else: ?>
                            <div class="w-8 h-8 bg-<?= $theme ?>-100 text-<?= $theme ?>-600 border border-<?= $theme ?>-200 rounded-full flex items-center justify-center font-bold text-sm group-hover:bg-<?= $theme ?>-600 group-hover:text-white transition">
                                <?= $initial ?>
                            </div>
                        <?php endif; ?>
                        
                        <span class="text-slate-700 group-hover:text-<?= $theme ?>-600 font-bold text-sm transition">
                            <?= htmlspecialchars($username) ?>
                        </span>
                    </a>
                    <a href="<?= $base ?>/logout" class="text-red-500 hover:text-red-700 font-medium text-sm transition bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg">
                        Logout
                    </a>
                </div>
            </div>

            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="text-slate-500 hover:text-<?= $theme ?>-600 focus:outline-none transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 shadow-xl absolute w-full pb-4">
        
        <div class="px-4 py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
            <?php if ($navAvatarSrc): ?>
                <img src="<?= htmlspecialchars($navAvatarSrc) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-<?= $theme ?>-200 shadow-sm">
            <?php else: ?>
                <div class="w-10 h-10 bg-<?= $theme ?>-100 text-<?= $theme ?>-600 rounded-full flex items-center justify-center font-bold text-lg border border-<?= $theme ?>-200 shadow-sm">
                    <?= $initial ?>
                </div>
            <?php endif; ?>
            
            <div>
                <p class="font-bold text-slate-800 text-sm">@<?= htmlspecialchars($username) ?></p>
                <a href="<?= $base ?>/profil" class="text-xs text-<?= $theme ?>-600 font-medium hover:underline">Pengaturan Profil</a>
            </div>
        </div>
        
        <div class="px-3 pt-3 space-y-1">
            <a href="<?= $base ?>/dashboard" class="block px-3 py-2.5 rounded-lg text-slate-600 hover:bg-<?= $theme ?>-50 hover:text-<?= $theme ?>-600 font-medium text-sm transition">Dashboard</a>
            <a href="<?= $base ?>/absen" class="block px-3 py-2.5 rounded-lg text-slate-600 hover:bg-<?= $theme ?>-50 hover:text-<?= $theme ?>-600 font-medium text-sm transition">Absen Olahraga</a>
            <a href="<?= $base ?>/kalender" class="block px-3 py-2.5 rounded-lg text-slate-600 hover:bg-<?= $theme ?>-50 hover:text-<?= $theme ?>-600 font-medium text-sm transition">Kalender</a>
            <a href="<?= $base ?>/logout" class="block px-3 py-2.5 mt-4 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 font-medium text-sm text-center transition">Logout</a>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        if(btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    });
</script>