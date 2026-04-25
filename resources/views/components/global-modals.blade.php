@if(auth()->check())
{{-- GLOBAL CREATE POST MODAL --}}
<div class="modal fade" id="createPostModal" tabindex="-1" style="z-index: 99999 !important;">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-ig">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Postingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                            <select name="visibility" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 mt-1" style="font-size: 0.75rem; width: auto;">
                                <option value="public">🌍 Publik</option>
                                <option value="friends">👥 Alumni Saja</option>
                            </select>
                        </div>
                    </div>
                    <textarea name="content" class="form-control border-0 bg-transparent p-0 fs-5 mb-3" rows="5" placeholder="Apa yang sedang terjadi?" required style="box-shadow: none;"></textarea>
                    
                    <div id="image-preview-container-global" class="position-relative d-none mb-3">
                        <img id="image-preview-global" src="#" class="w-100 rounded-3 shadow-sm">
                        <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-2" onclick="removeGlobalImage()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small">Tambahkan ke postingan</span>
                        <div class="d-flex gap-2">
                            <label class="btn btn-light rounded-circle p-2 cursor-pointer mb-0">
                                <i class="bi bi-image text-success fs-5"></i>
                                <input type="file" name="image" id="post-image-input-global" class="d-none" accept="image/*">
                            </label>
                            <button type="button" class="btn btn-light rounded-circle p-2"><i class="bi bi-emoji-smile text-warning fs-5"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-bold py-2">POSTING</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- GLOBAL CREATE STORY MODAL --}}
<div class="modal fade" id="createStoryModal" tabindex="-1" style="z-index: 99999 !important;">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-ig">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Posting Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="story-preview-container-global" class="position-relative mb-3 bg-light rounded-4 d-flex align-items-center justify-content-center" style="min-height: 300px;">
                        <img id="story-preview-global" src="#" class="w-100 rounded-4 d-none">
                        <label for="story-input-global" class="btn btn-success rounded-pill px-4 py-2" id="story-label-global">
                            <i class="bi bi-camera-fill me-2"></i>Ambil Gambar
                        </label>
                        <input type="file" name="image" id="story-input-global" class="d-none" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Atau Bagikan Lagu Spotify</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-spotify text-success"></i></span>
                            <input type="url" name="spotify_url" class="form-control border-start-0 ps-0 shadow-none" placeholder="Paste link Spotify atau YouTube di sini...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Atau Bagikan Catatan (Hanya Teks)</label>
                        <textarea name="content" class="form-control border-0 bg-light rounded-3 py-2 mb-2" placeholder="Apa yang Anda pikirkan? (Maks 60 karakter)" maxlength="60" rows="2" style="resize: none;"></textarea>
                        
                        <div class="mood-picker d-flex gap-2 flex-wrap mb-2">
                            <input type="hidden" name="caption" id="selected-mood" value="default">
                            <button type="button" onclick="selectMood('default', this)" class="btn btn-dark rounded-circle p-0 border border-2 border-primary" style="width: 25px; height: 25px; background: #121212;"></button>
                            <button type="button" onclick="selectMood('sunset', this)" class="btn rounded-circle p-0 border-0" style="width: 25px; height: 25px; background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);"></button>
                            <button type="button" onclick="selectMood('ocean', this)" class="btn rounded-circle p-0 border-0" style="width: 25px; height: 25px; background: linear-gradient(45deg, #2af598 0%, #009efd 100%);"></button>
                            <button type="button" onclick="selectMood('midnight', this)" class="btn rounded-circle p-0 border-0" style="width: 25px; height: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></button>
                            <button type="button" onclick="selectMood('forest', this)" class="btn rounded-circle p-0 border-0" style="width: 25px; height: 25px; background: linear-gradient(to right, #43e97b 0%, #38f9d7 100%);"></button>
                        </div>
                    </div>

                    <script>
                        function selectMood(mood, btn) {
                            document.getElementById('selected-mood').value = mood;
                            document.querySelectorAll('.mood-picker button').forEach(b => b.classList.remove('border', 'border-2', 'border-primary'));
                            btn.classList.add('border', 'border-2', 'border-primary');
                        }
                    </script>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">Keterangan (Opsional)</label>
                        <input type="text" name="caption" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Tambahkan keterangan..." maxlength="100">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-bold py-2">BAGIKAN CERITA</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
        (function() {
            function initModals() {
                console.log('Senior Diagnostic: Initializing Global Modals');
                
                // Global Post Preview
                const postInput = document.getElementById('post-image-input-global');
                if (postInput) {
                    postInput.addEventListener('change', function(e) {
                        if (!e.target.files || !e.target.files[0]) return;
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.getElementById('image-preview-global');
                            if (preview) {
                                preview.src = event.target.result;
                                const container = document.getElementById('image-preview-container-global');
                                if (container) container.classList.remove('d-none');
                            }
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    });
                }

                // Global Story Preview
                const storyInput = document.getElementById('story-input-global');
                if (storyInput) {
                    storyInput.addEventListener('change', function(e) {
                        if (!e.target.files || !e.target.files[0]) return;
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.getElementById('story-preview-global');
                            const label = document.getElementById('story-label-global');
                            if (preview) {
                                preview.src = event.target.result;
                                preview.classList.remove('d-none');
                            }
                            if (label) label.classList.add('d-none');
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    });
                }

                // Character Counter for Story
                const storyContent = document.querySelector('textarea[name="content"]');
                if (storyContent) {
                    storyContent.addEventListener('input', function() {
                        const remaining = 60 - this.value.length;
                        this.placeholder = `Apa yang Anda pikirkan? (${remaining} karakter tersisa)`;
                    });
                }

                // Generic Loading State for all forms in modals
                document.querySelectorAll('.modal form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const btn = this.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            const originalText = btn.innerHTML;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>MENGIRIM...';
                            
                            // Emergency Timeout: if it takes more than 10s, something is likely wrong with the connection
                            setTimeout(() => {
                                if (btn && btn.disabled && btn.innerHTML.includes('MENGIRIM')) {
                                    btn.disabled = false;
                                    btn.innerHTML = originalText;
                                    console.warn('Senior Diagnostic: Submission taking too long, possibly a network issue.');
                                    alert('Koneksi terputus atau server sedang lambat. Silakan coba lagi.');
                                }
                            }, 10000);
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initModals);
            } else {
                initModals();
            }
        })();

        function removeGlobalImage() {
            const input = document.getElementById('post-image-input-global');
            const container = document.getElementById('image-preview-container-global');
            if (input) input.value = '';
            if (container) container.classList.add('d-none');
        }
    </script>
@endif
