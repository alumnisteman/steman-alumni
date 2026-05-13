/**
 * Steman Lens - Premium Command Palette
 * Shortcut: Ctrl + K
 */

document.addEventListener('DOMContentLoaded', function() {
    const paletteHtml = `
    <div class="modal fade" id="stemanLensModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                <div class="modal-body p-0">
                    <div class="p-4 border-bottom">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 fs-4 text-primary"><i class="bi bi-search"></i></span>
                            <input type="text" id="lensSearchInput" class="form-control bg-transparent border-0 fs-5 shadow-none" placeholder="Cari menu, alumni, atau perintah... (Alt untuk navigasi)" autocomplete="off">
                            <span class="badge bg-light text-muted d-flex align-items-center px-2 py-0 border" style="height: 24px; font-size: 0.6rem;">ESC</span>
                        </div>
                    </div>
                    <div id="lensResults" class="overflow-auto" style="max-height: 400px; padding: 1rem;">
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="bi bi-command display-4 d-block mb-2"></i>
                            <p class="small">Ketik sesuatu untuk memulai pencarian...</p>
                        </div>
                    </div>
                    <div class="bg-light p-3 border-top d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3 small text-muted">
                            <span><kbd class="bg-white border text-dark shadow-sm">↑↓</kbd> Navigasi</span>
                            <span><kbd class="bg-white border text-dark shadow-sm">↵</kbd> Pilih</span>
                        </div>
                        <div class="text-primary fw-bold small">Steman Lens <span class="badge bg-primary text-white ms-1">PRO</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', paletteHtml);

    const lensModal = new bootstrap.Modal(document.getElementById('stemanLensModal'));
    const searchInput = document.getElementById('lensSearchInput');
    const resultsContainer = document.getElementById('lensResults');

    const commands = [
        { title: 'Dashboard Utama', icon: 'bi-speedometer2', url: '/admin', group: 'Navigasi' },
        { title: 'Manajemen Alumni', icon: 'bi-people', url: '/admin/users', group: 'Data' },
        { title: 'Buat Berita Baru', icon: 'bi-plus-circle', url: '/admin/news/create', group: 'Konten' },
        { title: 'Lihat Daftar Berita', icon: 'bi-newspaper', url: '/admin/news', group: 'Konten' },
        { title: 'Manajemen Galeri', icon: 'bi-images', url: '/admin/gallery', group: 'Konten' },
        { title: 'Program & Event', icon: 'bi-calendar-event', url: '/admin/programs', group: 'Navigasi' },
        { title: 'Lowongan Kerja', icon: 'bi-briefcase', url: '/admin/jobs', group: 'Data' },
        { title: 'Pesan Masuk', icon: 'bi-chat-dots', url: '/admin/messages', group: 'Data' },
        { title: 'Pengaturan Situs', icon: 'bi-gear', url: '/admin/settings', group: 'Sistem' },
        { title: 'System Logs', icon: 'bi-terminal', url: '/admin/system/logs', group: 'Sistem' },
        { title: 'Manajemen Iklan', icon: 'bi-megaphone', url: '/admin/ads', group: 'Bisnis' },
        { title: 'Moderasi Bisnis', icon: 'bi-shop', url: '/admin/business', group: 'Bisnis' },
    ];

    let selectedIndex = -1;

    // Open palette on Ctrl + K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            lensModal.show();
        }
    });

    document.getElementById('stemanLensModal').addEventListener('shown.bs.modal', () => {
        searchInput.focus();
        renderResults('');
    });

    searchInput.addEventListener('input', (e) => {
        renderResults(e.target.value);
    });

    function renderResults(query) {
        const filtered = commands.filter(cmd => 
            cmd.title.toLowerCase().includes(query.toLowerCase()) || 
            cmd.group.toLowerCase().includes(query.toLowerCase())
        );

        if (filtered.length === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                    <p>Tidak ditemukan hasil untuk "${query}"</p>
                </div>`;
            return;
        }

        let currentGroup = '';
        let html = '';
        filtered.forEach((cmd, index) => {
            if (cmd.group !== currentGroup) {
                currentGroup = cmd.group;
                html += `<div class="text-uppercase small fw-bold text-primary opacity-75 mt-3 mb-2 px-2" style="font-size: 0.65rem; letter-spacing: 1px;">${currentGroup}</div>`;
            }
            html += `
                <a href="${cmd.url}" class="lens-item d-flex align-items-center p-3 rounded-3 text-decoration-none transition-all mb-1 ${index === selectedIndex ? 'active' : ''}" data-index="${index}">
                    <div class="lens-icon bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                        <i class="bi ${cmd.icon} text-dark"></i>
                    </div>
                    <div class="flex-grow-1 text-dark fw-semibold">${cmd.title}</div>
                    <i class="bi bi-arrow-return-left small text-muted opacity-25"></i>
                </a>
            `;
        });
        resultsContainer.innerHTML = html;
        selectedIndex = -1; // Reset selection on new search
    }

    // Keyboard navigation
    searchInput.addEventListener('keydown', (e) => {
        const items = document.querySelectorAll('.lens-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection();
        } else if (e.key === 'Enter') {
            if (selectedIndex > -1) {
                items[selectedIndex].click();
            } else if (items.length > 0) {
                items[0].click();
            }
        }
    });

    function updateSelection() {
        const items = document.querySelectorAll('.lens-item');
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('active', 'bg-primary', 'bg-opacity-10', 'border-start', 'border-primary', 'border-4');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active', 'bg-primary', 'bg-opacity-10', 'border-start', 'border-primary', 'border-4');
            }
        });
    }
});
