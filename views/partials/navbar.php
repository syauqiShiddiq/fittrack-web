<?php $base = '/fittrack-web'; ?>
<nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="<?= $base ?>/dashboard" class="text-xl font-bold text-sky-600">FitTrack</a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="<?= $base ?>/dashboard" class="text-slate-600 hover:text-sky-600 font-medium transition">Dashboard</a>
                <a href="<?= $base ?>/absen" class="text-slate-600 hover:text-sky-600 font-medium transition">Absen Olahraga</a>
                <a href="<?= $base ?>/kalender" class="text-slate-600 hover:text-sky-600 font-medium transition">Kalender</a>
                <a href="<?= $base ?>/logout" class="text-red-500 hover:text-red-700 font-medium transition">Logout</a>
            </div>
            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100 shadow-md absolute w-full">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="<?= $base ?>/dashboard" class="block px-3 py-2 rounded-md text-slate-600 hover:bg-slate-50 hover:text-sky-600 font-medium">Dashboard</a>
            <a href="<?= $base ?>/absen" class="block px-3 py-2 rounded-md text-slate-600 hover:bg-slate-50 hover:text-sky-600 font-medium">Absen Olahraga</a>
            <a href="<?= $base ?>/kalender" class="block px-3 py-2 rounded-md text-slate-600 hover:bg-slate-50 hover:text-sky-600 font-medium">Kalender</a>
            <a href="<?= $base ?>/logout" class="block px-3 py-2 rounded-md text-red-500 hover:bg-slate-50 font-medium">Logout</a>
        </div>
    </div>
</nav>