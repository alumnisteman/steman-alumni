@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="section-title mb-0">Manajemen Pengguna</h2>
        @if(in_array(auth()->user()->role, ['admin', 'editor']))
        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna
        </button>
        @endif
    </div>

    @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">NAMA</th>
                            <th>EMAIL</th>
                            <th>ROLE</th>
                            <th>STATUS</th>
                            <th>TANGGAL DAFTAR</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                         class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleBadge = match($user->role) {
                                        'admin'  => ['bg-danger',  'ADMIN'],
                                        'editor' => ['bg-info text-white', 'EDITOR'],
                                        default  => ['bg-primary', 'ALUMNI'],
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge[0] }} rounded-pill px-3">{{ $roleBadge[1] }}</span>
                            </td>
                            <td>
                                @if($user->status == 'approved')
                                    <span class="badge bg-success rounded-pill px-3">APPROVED</span>
                                @elseif($user->status == 'rejected')
                                    <span class="badge bg-danger rounded-pill px-3">REJECTED</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3">PENDING</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>

                                <form action="/admin/users/{{ $user->id }}/role" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <div class="btn-group me-1">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                            <i class="bi bi-shield-check"></i> Role
                                        </button>
                                        <ul class="dropdown-menu shadow-sm">
                                            @if(auth()->user()->role === 'admin' && $user->role !== 'admin')<li><button class="dropdown-item" type="submit" name="role" value="admin">Jadikan Admin</button></li>@endif
                                            @if($user->role !== 'editor')<li><button class="dropdown-item" type="submit" name="role" value="editor">Jadikan Editor</button></li>@endif
                                            @if($user->role !== 'alumni')<li><button class="dropdown-item" type="submit" name="role" value="alumni">Jadikan Alumni</button></li>@endif
                                        </ul>
                                    </div>
                                </form>

                                @if($user->role !== 'admin')
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST" class="m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm {{ $user->status === 'approved' ? 'btn-success' : 'btn-outline-success' }}" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST" class="m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm {{ $user->status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}" title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST" class="m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn btn-sm {{ $user->status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}" title="Pending">
                                            <i class="bi bi-hourglass-split"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif

                                @if(auth()->user()->role === 'admin' && $user->id !== auth()->id())
                                <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </button>
                                @elseif(auth()->user()->role === 'editor' && $user->role === 'alumni')
                                <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </button>
                                @endif
                            </td>
                        </tr>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if($users->total() > 20)
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif
</div>

@foreach($users as $user)
{{-- Modal Konfirmasi Hapus User --}}
@if(auth()->user()->role === 'admin' && $user->id !== auth()->id())
<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
                        class="rounded-circle" width="70" height="70" style="object-fit:cover;">
                </div>
                <p class="mb-1">Anda yakin ingin menghapus pengguna ini?</p>
                <p class="fw-bold fs-5 text-danger">{{ $user->name }}</p>
                <p class="text-muted small">{{ $user->email }}</p>
                <div class="alert alert-warning small py-2 border-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data user akan dihapus permanen.
                </div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center" style="border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <form action="/admin/users/{{ $user->id }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 fw-bold">
                        <i class="bi bi-trash-fill me-1"></i>Ya, Hapus Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Edit User Modal -->
<div class="modal fade text-start" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
        <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $user->id }}">Edit Profil: {{ $user->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/users/{{ $user->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-12">
                    <label class="form-label fw-bold">Foto Profil</label>
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                        <input type="file" name="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror" accept="image/*">
                        @error('profile_picture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">NISN</label>
                    <input type="text" name="nisn" class="form-control" value="{{ $user->nisn }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tahun Lulus</label>
                    <input type="number" name="graduation_year" class="form-control" value="{{ $user->graduation_year }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">major</label>
                    <select name="major" class="form-select">
                        <option value="">-- Pilih major --</option>
                        @php 
                            $currentGroup = ''; 
                            $found = false;
                            $usermajor = $user->major ?? '';
                        @endphp
                        @foreach($activeMajors as $m)
                            @if($currentGroup != $m->group)
                                @if($currentGroup != '') </optgroup> @endif
                                <optgroup label="{{ $m->group == 'Modern' ? 'Kurikulum Saat Ini' : 'Kurikulum Lama (Legacy)' }}">
                                @php $currentGroup = $m->group; @endphp
                            @endif
                            @php 
                                $isSelected = (old('major') ?? $usermajor) == $m->name;
                                if($isSelected) $found = true;
                            @endphp
                            <option value="{{ $m->name }}" {{ $isSelected ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                        @if($currentGroup != '') </optgroup> @endif
                        
                        {{-- Robust Fallback: If user's current value isn't in the master list, show it --}}
                        @if(!$found && !empty($usermajor))
                            <optgroup label="Data Saat Ini (Tidak Sinkron)">
                                <option value="{{ $usermajor }}" selected>{{ $usermajor }} (Mohon Update ke Master Data)</option>
                            </optgroup>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ $user->phone_number }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">address Domisili</label>
                    <textarea name="address" class="form-control" rows="2">{{ $user->address }}</textarea>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold">Bio / Deskripsi Singkat</label>
                    <textarea name="bio" class="form-control" rows="3">{{ $user->bio }}</textarea>
                </div>

                <div class="col-12 mt-4">
                    <div class="alert alert-info py-2 small border-0 shadow-sm" style="border-radius: 10px;">
                        <i class="bi bi-info-circle me-2"></i> Kosongkan password jika tidak ingin mengganti.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Password Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="********">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="********">
                </div>
            </div>
        </div>
        <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 15px 15px;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
        </div>
        </form>
    </div>
    </div>
</div>
@endforeach

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
          <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
            <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            {{-- Penanda form agar modal bisa dibuka ulang jika ada error --}}
            <input type="hidden" name="_form" value="add_user">
            <div class="modal-body p-4">
                @if(old('_form') == 'add_user' && $errors->any())
                    <div class="alert alert-danger border-0 small py-2">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @if(old('_form')=='add_user') {{ $errors->has('name') ? 'is-invalid' : '' }} @endif"
                           required placeholder="Masukkan nama..." value="{{ old('_form') == 'add_user' ? old('name') : '' }}">
                    @if(old('_form') == 'add_user') @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @if(old('_form')=='add_user') {{ $errors->has('email') ? 'is-invalid' : '' }} @endif"
                           required placeholder="email@example.com" value="{{ old('_form') == 'add_user' ? old('email') : '' }}">
                    @if(old('_form') == 'add_user') @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Role</label>
                    <select name="role" class="form-select" required>
                        @foreach(\App\Models\User::ROLES as $roleOption)
                            <option value="{{ $roleOption }}"
                                {{ old('_form')=='add_user' && old('role')==$roleOption ? 'selected' : '' }}>
                                {{ ucfirst($roleOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">major (Untuk Alumni)</label>
                    <select name="major" class="form-select">
                        <option value="">-- Pilih major --</option>
                        @php $currentGroup = ''; @endphp
                        @foreach($activeMajors as $m)
                            @if($currentGroup != $m->group)
                                @if($currentGroup != '') </optgroup> @endif
                                <optgroup label="{{ $m->group == 'Modern' ? 'Kurikulum Saat Ini' : 'Kurikulum Lama (Legacy)' }}">
                                @php $currentGroup = $m->group; @endphp
                            @endif
                            <option value="{{ $m->name }}" {{ old('major') == $m->name ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                        @if($currentGroup != '') </optgroup> @endif
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @if(old('_form')=='add_user') {{ $errors->has('password') ? 'is-invalid' : '' }} @endif"
                               required placeholder="Min. 4 karakter">
                        @if(old('_form') == 'add_user') @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan User</button>
            </div>
          </form>
        </div>
      </div>
    </div>

@push('scripts')
<script>
// Auto-buka modal tambah user jika ada error dari form tambah user
document.addEventListener('DOMContentLoaded', function() {
    @if(old('_form') == 'add_user' && $errors->any())
        var myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        myModal.show();
    @endif
});
</script>
@endpush

@endsection
