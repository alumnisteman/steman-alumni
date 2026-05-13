@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="section-title mb-0">Kelola Galeri</h2>
        <button class="btn btn-alumni_smkn2" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-plus-lg me-2"></i>Tambah Media
        </button>
    </div>

    @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div> @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 100px;">AKSI</th>
                        <th style="width: 100px;">PREVIEW</th>
                        <th>JUDUL</th>
                        <th>TIPE</th>
                        <th>STATUS</th>
                        <th>SUMBER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($media as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($media->currentPage() - 1) * $media->perPage() }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white shadow-sm" 
                                   style="position: relative; z-index: 9999; cursor: pointer !important; border: 2px solid white;"
                                   onclick="var mId = 'editModal{{ $item->id }}'; var mEl = document.getElementById(mId); if(mEl) { mEl.classList.add('show'); mEl.style.display='block'; document.body.classList.add('modal-open'); } else { alert('Modal ' + mId + ' tidak ditemukan!'); }"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#editModal{{ $item->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form id="delete-gallery-{{ $item->id }}" action="{{ route('admin.gallery.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger shadow-sm" onclick="window.Guardian.confirmDelete('delete-gallery-{{ $item->id }}')">
                                        <i class="bi bi-trash3" style="pointer-events: none;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td>
                            @if($item->type == 'photo')
                                <img src="{{ $item->file_path }}" alt="" class="rounded" style="width: 80px; height: 50px; object-fit: cover; border: 1px solid #eee;">
                            @elseif($item->type == 'video')
                                <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 50px;">
                                    <i class="bi bi-play-circle text-white fs-4"></i>
                                </div>
                            @elseif($item->type == 'tiktok')
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 50px;">
                                    <i class="bi bi-tiktok text-white fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $item->title }}</td>
                        <td>
                            @if($item->type == 'photo')
                                <span class="badge bg-primary bg-opacity-10 text-primary border-primary border-opacity-25 border">Foto</span>
                            @elseif($item->type == 'video')
                                <span class="badge bg-danger bg-opacity-10 text-danger border-danger border-opacity-25 border">YouTube</span>
                            @elseif($item->type == 'tiktok')
                                <span class="badge bg-dark bg-opacity-10 text-dark border-dark border-opacity-25 border">TikTok</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'published')
                                <span class="badge bg-success bg-opacity-10 text-success border-success border-opacity-25 border"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25 border"><i class="bi bi-dash-circle me-1"></i>Draft</span>
                            @endif
                        </td>
                        <td>
                            @if($item->youtube_url)
                                <span class="text-muted small"><i class="bi bi-youtube me-1"></i>YouTube</span>
                            @elseif($item->tiktok_url)
                                <span class="text-muted small"><i class="bi bi-tiktok me-1"></i>TikTok</span>
                            @else
                                <span class="text-muted small"><i class="bi bi-hdd me-1"></i>Lokal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-images d-block fs-3 mb-2"></i> Belum ada media di galeri.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($media as $item)
<!-- Edit Modal for item {{ $item->id }} -->
<div class="modal text-start" id="editModal{{ $item->id }}" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning bg-opacity-10 p-4 rounded-top-4">
                <h5 class="modal-title fw-bold">✏️ Edit Media</h5>
                <button type="button" class="btn-close" onclick="this.closest('.modal').style.display='none'; document.body.classList.remove('modal-open');" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.gallery.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Media</label>
                        <select name="type" class="form-select edit-type-select" data-id="{{ $item->id }}" required>
                            <option value="photo" {{ $item->type == 'photo' ? 'selected' : '' }}>Foto</option>
                            <option value="youtube" {{ $item->type == 'youtube' ? 'selected' : '' }}>Video (YouTube)</option>
                            <option value="tiktok" {{ $item->type == 'tiktok' ? 'selected' : '' }}>🎵 TikTok</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Display</label>
                        <select name="status" class="form-select" required>
                            <option value="published" {{ $item->status == 'published' ? 'selected' : '' }}>Diterbitkan (Published)</option>
                            <option value="draft" {{ $item->status == 'draft' ? 'selected' : '' }}>Draf (Draft)</option>
                        </select>
                    </div>
                    
                    <div class="editGroup-photo-{{ $item->id }} {{ $item->type == 'photo' ? '' : 'd-none' }}">
                        @if($item->type == 'photo' && $item->file_path)
                            <div class="mb-3 text-center text-md-start">
                                <img src="{{ $item->file_path }}" class="img-thumbnail" style="height:100px; object-fit:cover;">
                            </div>
                        @endif
                    </div>
                    
                    <div class="editGroup-video-{{ $item->id }} {{ $item->type == 'youtube' ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Link YouTube</label>
                            <input type="url" name="youtube_url" class="form-control" value="{{ $item->type == 'youtube' ? $item->youtube_url : '' }}" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <div class="edit-file-group-{{ $item->id }} {{ $item->type != 'photo' ? 'd-none' : '' }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold edit-file-label-{{ $item->id }}">Ganti Foto (Opsional)</label>
                            <input type="file" name="file" class="form-control" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="editGroup-tiktok-{{ $item->id }} {{ $item->type == 'tiktok' ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-music-note-beamed me-1"></i>Link TikTok</label>
                            <input type="url" name="tiktok_url" class="form-control" value="{{ $item->type == 'tiktok' ? $item->tiktok_url : '' }}" placeholder="https://www.tiktok.com/@...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" onclick="this.closest('.modal').style.display='none'; document.body.classList.remove('modal-open');" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0" style="border-radius: 20px;">
            @csrf
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Tambah Media Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Judul Kegiatan</label>
                        <input type="text" name="title" class="form-control" placeholder="Masukkan judul..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tipe Media</label>
                        <select name="type" class="form-select" id="typeSelect" required>
                            <option value="photo">Foto</option>
                            <option value="video">Video (YouTube)</option>
                            <option value="tiktok">🎵 TikTok</option>
                        </select>
                    </div>
                    
                    <div class="col-12" id="fileGroup">
                        <label class="form-label small fw-bold">File Foto (Max 5MB)</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <div class="col-12 d-none" id="youtubeGroup">
                        <label class="form-label small fw-bold">Link YouTube</label>
                        <input type="url" name="youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <div class="col-md-6 d-none" id="tiktokGroup">
                        <label class="form-label small fw-bold"><i class="bi bi-music-note-beamed me-1"></i>Link TikTok</label>
                        <input type="url" name="tiktok_url" class="form-control" placeholder="https://www.tiktok.com/@...">
                    </div>

                    <div class="col-md-6" id="statusGroup">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="published" selected>Diterbitkan (Published)</option>
                            <option value="draft">Draf (Draft)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Ceritakan singkat tentang momen ini..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-alumni_smkn2 rounded-pill px-4 shadow">Simpan Media</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('typeSelect').addEventListener('change', function() {
        const type = this.value;
        const youtubeGroup = document.getElementById('youtubeGroup');
        const tiktokGroup = document.getElementById('tiktokGroup');
        const fileGroup = document.getElementById('fileGroup');
        const fileLabel = fileGroup.querySelector('label');

        // Reset
        youtubeGroup.classList.add('d-none');
        tiktokGroup.classList.add('d-none');
        fileGroup.classList.remove('d-none');
        fileLabel.innerText = 'File Foto (Max 5MB)';

        if (type === 'video') {
            youtubeGroup.classList.remove('d-none');
            fileGroup.classList.add('d-none');
        } else if (type === 'tiktok') {
            tiktokGroup.classList.remove('d-none');
            fileGroup.classList.add('d-none');  // No file for TikTok
        }
    });

    document.querySelectorAll('.edit-type-select').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const type = this.value;
            const photoGroup = document.querySelector(`.editGroup-photo-${id}`);
            const videoGroup = document.querySelector(`.editGroup-video-${id}`);
            const tiktokGroup = document.querySelector(`.editGroup-tiktok-${id}`);
            const fileGroup = document.querySelector(`.edit-file-group-${id}`);
            const fileLabel = document.querySelector(`.edit-file-label-${id}`);
            const fileInput = fileGroup.querySelector('input[type="file"]');

            photoGroup.classList.add('d-none');
            videoGroup.classList.add('d-none');
            tiktokGroup.classList.add('d-none');
            fileGroup.classList.remove('d-none');

            if (type === 'photo') {
                photoGroup.classList.remove('d-none');
                fileGroup.classList.remove('d-none');
                fileLabel.innerText = 'Ganti Foto (Opsional)';
                fileInput.accept = 'image/*';
            } else if (type === 'video') {
                videoGroup.classList.remove('d-none');
            } else if (type === 'tiktok') {
                tiktokGroup.classList.remove('d-none');
                fileGroup.classList.add('d-none');
            }
        });
    });
</script>
@endsection

