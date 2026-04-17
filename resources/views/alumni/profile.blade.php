@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="section-title">Edit Profil Saya</h2>
            
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="/alumni/profile" method="POST" enctype="multipart/form-data" class="card p-5 shadow-sm border-0">
                @csrf @method('PUT')
                
                <div class="text-center mb-4">
                    <img src="{{ $user->profile_picture_url }}" 
                         class="rounded-circle border" width="150" height="150" style="object-fit: cover;">
                    <div class="mt-3">
                        <label class="btn btn-sm btn-outline-primary shadow-none">
                            Ganti Foto Profil
                            <input type="file" name="profile_picture" class="d-none">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Tetap)</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">major</label>
                        <select name="major" class="form-select">
                            <option value="">Pilih major</option>
                            @php 
                                $currentGroup = ''; 
                                $found = false;
                                $usermajor = $user->major ?? '';
                            @endphp
                            @foreach($majors as $major)
                                @if($currentGroup != $major->group)
                                    @if($currentGroup != '') </optgroup> @endif
                                    <optgroup label="{{ $major->group == 'Modern' ? 'Kurikulum Saat Ini' : 'Kurikulum Lama (Legacy)' }}">
                                    @php $currentGroup = $major->group; @endphp
                                @endif
                                @php 
                                    $isSelected = $usermajor == $major->name;
                                    if($isSelected) $found = true;
                                @endphp
                                <option value="{{ $major->name }}" {{ $isSelected ? 'selected' : '' }}>{{ $major->name }}</option>
                            @endforeach
                            @if($currentGroup != '') </optgroup> @endif

                            {{-- Fallback matching: If user data doesn't match master list exactly --}}
                            @if(!$found && !empty($usermajor))
                                <optgroup label="Data Saat Ini (Beda Format)">
                                    <option value="{{ $usermajor }}" selected>{{ $usermajor }}</option>
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" name="graduation_year" class="form-control" value="{{ $user->graduation_year }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pekerjaan Sekarang</label>
                    <input type="text" name="current_job" class="form-control" value="{{ $user->current_job }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">address</label>
                    <textarea name="address" class="form-control" rows="2">{{ $user->address }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tentang Saya (Bio)</label>
                    <textarea name="bio" class="form-control" rows="4">{{ $user->bio }}</textarea>
                </div>

                <div class="card bg-primary-subtle border-0 rounded-4 p-4 mb-4 mt-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_mentor" id="isMentorSwitch" {{ $user->is_mentor ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold h5 mb-0" for="isMentorSwitch">
                            Daftar sebagai Mentor Alumni
                        </label>
                    </div>
                    <div id="mentorFields" class="{{ $user->is_mentor ? '' : 'd-none' }}">
                        <p class="small text-muted mb-3">Dengan mengaktifkan ini, alumni lain dapat melihat profil Anda di direktori mentor dan meminta bimbingan.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Keahlian Utama (Contoh: Web Development, Finance)</label>
                            <input type="text" name="mentor_expertise" class="form-control" value="{{ $user->mentor_expertise }}" placeholder="Keahlian yang ingin Anda bagikan...">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">Pesan Kesediaan Program Mentoring</label>
                            <textarea name="mentor_bio" class="form-control" rows="3" placeholder="Jelaskan bagaimana Anda bisa membantu adik-adik atau sesama alumni...">{{ $user->mentor_bio }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-share"></i> Jejaring Sosial</h5>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="show_social" id="showSocialSwitch" {{ $user->show_social ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold" for="showSocialSwitch">
                            Tampilkan Link Sosial Media di Profil Publik
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-instagram"></i> Instagram</label>
                            <input type="text" name="socials[instagram]" class="form-control" value="{{ $user->getSocialUrl('instagram') }}" placeholder="@username atau URL">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-facebook"></i> Facebook</label>
                            <input type="text" name="socials[facebook]" class="form-control" value="{{ $user->getSocialUrl('facebook') }}" placeholder="URL Profil">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-tiktok"></i> TikTok</label>
                            <input type="text" name="socials[tiktok]" class="form-control" value="{{ $user->getSocialUrl('tiktok') }}" placeholder="@username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-linkedin"></i> LinkedIn</label>
                            <input type="text" name="socials[linkedin]" class="form-control" value="{{ $user->getSocialUrl('linkedin') }}" placeholder="URL Profil">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-github"></i> GitHub</label>
                            <input type="text" name="socials[github]" class="form-control" value="{{ $user->getSocialUrl('github') }}" placeholder="Username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-globe"></i> Website Personal</label>
                            <input type="text" name="socials[website]" class="form-control" value="{{ $user->getSocialUrl('website') }}" placeholder="https://...">
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('isMentorSwitch').addEventListener('change', function() {
                        document.getElementById('mentorFields').classList.toggle('d-none', !this.checked);
                    });
                </script>

                <hr class="my-4">
                <h5 class="fw-bold mb-3">Ganti Password (Kosongkan jika tidak diganti)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-alumni_smkn2 btn-lg w-100 mt-3">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
