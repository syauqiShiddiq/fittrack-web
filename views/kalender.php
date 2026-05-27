<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        
        <div class="flex items-center justify-between mb-6">
            <h2 id="calendar-month-year" class="text-xl font-bold text-slate-800">Mei 2026</h2>
            <div class="flex gap-2">
                <button id="prev-month" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600">
                    &larr; Prev
                </button>
                <button id="next-month" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600">
                    Next &rarr;
                </button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center font-semibold text-slate-500 text-sm mb-2">
            <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
        </div>

        <div id="calendar-days" class="grid grid-cols-7 gap-2 min-h-[300px]"></div>

    </div>
</div>

<script>
let currentGridDate = new Date(); // default bulan ini
let workoutEvents = [];

// Fungsi untuk mengambil data olahraga via AJAX
async function fetchWorkoutDays() {
    try {
        const response = await fetch('/fittrack-web/api/workout-days');
        workoutEvents = await response.json();
        renderCalendar();
    } catch (error) {
        console.error("Gagal memuat data kalender:", error);
    }
}

function renderCalendar() {
    const daysContainer = document.getElementById('calendar-days');
    const monthYearText = document.getElementById('calendar-month-year');
    
    daysContainer.innerHTML = '';
    
    const year = currentGridDate.getFullYear();
    const month = currentGridDate.getMonth();
    
    // Set nama bulan Indonesia
    const monthsId = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktobor", "November", "Desember"];
    monthYearText.innerText = `${monthsId[month]} ${year}`;
    
    // Hari pertama pada bulan tersebut (0 = Minggu, 1 = Senin, dst)
    const firstDayIndex = new Date(year, month, 1).getDay();
    // Jumlah hari pada bulan tersebut
    const totalDays = new Date(year, month + 1, 0).getDate();
    
    // 1. Isi slot kosong untuk hari di bulan sebelumnya
    for (let i = 0; i < firstDayIndex; i++) {
        const emptyDiv = document.createElement('div');
        daysContainer.appendChild(emptyDiv);
    }
    
    // 2. Render tanggal asli bulan berjalan
    for (let day = 1; day <= totalDays; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = "h-14 border border-slate-100 rounded-xl flex flex-col items-center justify-center relative bg-slate-50/50";
        
        // Format tanggal string untuk dicocokkan dengan database (YYYY-MM-DD)
        const formatMonth = String(month + 1).padStart(2, '0');
        const formatDay = String(day).padStart(2, '0');
        const dateString = `${year}-${formatMonth}-${formatDay}`;
        
        // Cari tahu apakah user olahraga di tanggal ini
        const hasWorkout = workoutEvents.find(event => event.date === dateString);
        
        if (hasWorkout) {
            // Jika ada olahraga, ubah style tombolnya
            dayDiv.innerHTML = `
                <span class="w-8 h-8 flex items-center justify-center bg-sky-600 text-white font-bold rounded-full shadow-sm text-sm cursor-pointer" title="Total Durasi: ${hasWorkout.total_duration} Menit">
                    ${day}
                </span>
                <span class="text-[10px] text-sky-600 font-medium mt-1">🔥 ${hasWorkout.total_duration}m</span>
            `;
        } else {
            // Jika tidak ada olahraga, tampilkan angka biasa
            dayDiv.innerHTML = `<span class="text-sm font-medium text-slate-700">${day}</span>`;
        }
        
        daysContainer.appendChild(dayDiv);
    }
}

// Event Listener tombol navigasi bulan
document.getElementById('prev-month').addEventListener('click', () => {
    currentGridDate.setMonth(currentGridDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById('next-month').addEventListener('click', () => {
    currentGridDate.setMonth(currentGridDate.getMonth() + 1);
    renderCalendar();
});

// Jalankan pengambilan data saat halaman dibuka
fetchWorkoutDays();
</script>

<?php require 'partials/footer.php'; ?>