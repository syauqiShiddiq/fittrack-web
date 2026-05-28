<?php 
$theme = $_SESSION['theme_color'] ?? 'sky';
if (!in_array($theme, ['sky', 'emerald', 'amber', 'violet'])) $theme = 'sky';
?>
<?php require 'partials/header.php'; ?>
<?php require 'partials/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 py-8 relative">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        
        <div class="flex items-center justify-between mb-6">
            <h2 id="calendar-month-year" class="text-xl font-bold text-slate-800">Bulan Tahun</h2>
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

<div id="workout-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex justify-center items-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-fade-in-up">
        
        <div class="flex justify-between items-center p-4 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-800">Detail Aktivitas</h3>
            <button id="close-modal" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-4 space-y-4">
            <div id="modal-content" class="text-center text-slate-500">Memuat data...</div>
        </div>
    </div>
</div>

<script>
// Variabel warna untuk elemen antarmuka utama pengguna yang sedang login
const currentTheme = '<?= $theme ?>';

let currentGridDate = new Date();
let workoutEvents = [];
const modal = document.getElementById('workout-modal');
const modalContent = document.getElementById('modal-content');
const closeModalBtn = document.getElementById('close-modal');

// Mengambil tanggal-tanggal yang ada aktivitasnya dari server
async function fetchWorkoutDays() {
    try {
        const response = await fetch('/fittrack-web/api/workout-days');
        workoutEvents = await response.json();
        renderCalendar();
    } catch (error) { console.error("Gagal memuat data kalender:", error); }
}

// Memuat isi aktivitas saat sebuah tanggal spesifik diklik
async function fetchWorkoutDetails(dateString) {
    modal.classList.remove('hidden');
    modalContent.innerHTML = `<div class="py-10 text-slate-400 font-medium animate-pulse">Menyiapkan detail aktivitas...</div>`;
    
    try {
        const response = await fetch(`/fittrack-web/api/workout-details?date=${dateString}`);
        const details = await response.json();
        
        if (details.length > 0) {
            let htmlContent = '';
            details.forEach(item => {
                let imgUrl = item.photo_url;
                const match = imgUrl.match(/d\/([a-zA-Z0-9_-]+)/);
                if (match) imgUrl = `https://drive.google.com/thumbnail?id=${match[1]}&sz=w1000`;

                const userTheme = item.theme_color || 'sky';
                
                // Cek dan render Avatar Profile untuk Kalender
                let avatarHtml = '';
                if (item.avatar_url) {
                    const avatarMatch = item.avatar_url.match(/d\/([a-zA-Z0-9_-]+)/);
                    if (avatarMatch) {
                        const avatarSrc = `https://drive.google.com/thumbnail?id=${avatarMatch[1]}&sz=w100`;
                        avatarHtml = `<img src="${avatarSrc}" class="w-8 h-8 rounded-full object-cover border border-${userTheme}-200 shadow-sm" alt="Avatar">`;
                    }
                }
                
                // Fallback Inisial Nama jika tidak ada URL Avatar
                if (!avatarHtml) {
                    avatarHtml = `
                        <div class="w-8 h-8 bg-${userTheme}-100 text-${userTheme}-600 rounded-full flex items-center justify-center font-bold text-sm border border-${userTheme}-200">
                            ${item.username.charAt(0).toUpperCase()}
                        </div>
                    `;
                }

                htmlContent += `
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 mb-4 text-left">
                        <div class="flex items-center gap-3 mb-3">
                            ${avatarHtml}
                            <div>
                                <p class="font-bold text-slate-800 text-sm">${item.username}</p>
                                <p class="text-[11px] text-slate-500 font-medium">⏱ ${item.duration} Menit</p>
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm mb-3">${item.description}</p>
                        <img src="${imgUrl}" alt="Bukti" class="w-full h-40 object-cover rounded-lg border border-slate-200 shadow-sm">
                    </div>
                `;
            });
            modalContent.innerHTML = htmlContent;
        } else {
            modalContent.innerHTML = `<p class="py-6 text-slate-500">Tidak ada detail yang ditemukan.</p>`;
        }
    } catch (error) {
        modalContent.innerHTML = `<p class="py-6 text-red-500">Terjadi kesalahan saat memuat data.</p>`;
    }
}

// Merender struktur HTML grid kalender
function renderCalendar() {
    const daysContainer = document.getElementById('calendar-days');
    const monthYearText = document.getElementById('calendar-month-year');
    daysContainer.innerHTML = '';
    
    const year = currentGridDate.getFullYear();
    const month = currentGridDate.getMonth();
    const monthsId = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    monthYearText.innerText = `${monthsId[month]} ${year}`;
    
    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    
    for (let i = 0; i < firstDayIndex; i++) daysContainer.appendChild(document.createElement('div'));
    
    for (let day = 1; day <= totalDays; day++) {
        const dayDiv = document.createElement('div');
        dayDiv.className = "h-14 border border-slate-100 rounded-xl flex flex-col items-center justify-center relative bg-slate-50/50";
        
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const hasWorkout = workoutEvents.find(event => event.date === dateString);
        
        if (hasWorkout) {
            // Tombol tanggal dan ikon api menggunakan currentTheme agar selaras dengan UI user
            dayDiv.innerHTML = `
                <div onclick="fetchWorkoutDetails('${dateString}')" class="w-8 h-8 flex items-center justify-center bg-${currentTheme}-600 text-white font-bold rounded-full shadow-sm text-sm cursor-pointer hover:bg-${currentTheme}-700 transition transform hover:scale-110">
                    ${day}
                </div>
                <span class="text-[10px] text-${currentTheme}-600 font-medium mt-1">🔥 ${hasWorkout.total_duration}m</span>
            `;
        } else {
            dayDiv.innerHTML = `<span class="text-sm font-medium text-slate-700">${day}</span>`;
        }
        daysContainer.appendChild(dayDiv);
    }
}

// Event Listeners
document.getElementById('prev-month').addEventListener('click', () => { currentGridDate.setMonth(currentGridDate.getMonth() - 1); renderCalendar(); });
document.getElementById('next-month').addEventListener('click', () => { currentGridDate.setMonth(currentGridDate.getMonth() + 1); renderCalendar(); });
closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.add('hidden'); });

// Inisialisasi awal
fetchWorkoutDays();
</script>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.2s ease-out forwards; }
</style>

<?php require 'partials/footer.php'; ?>