@extends('layouts.app')

@section('content')
@php $currentUser = auth()->user(); @endphp
<div style="margin-bottom:1rem; color:#555;">Logged in as: {{ $currentUser ? $currentUser->email : 'guest' }}</div>
<style>
.poll-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
    padding: 60px 0 40px;
    position: relative; overflow: hidden;
}
.poll-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.08) 0%, transparent 60%);
}

/* Poll Card */
.poll-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: box-shadow 0.3s;
    margin-bottom: 1.5rem;
}
.poll-card:hover { box-shadow: 0 10px 40px rgba(79,70,229,0.12); }
.poll-card .poll-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.poll-emoji { font-size: 2.5rem; }
.poll-card .poll-body { padding: 1.2rem 1.5rem 1.5rem; }

/* Option Button */
.poll-option-btn {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 16px;
    text-align: left;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
    margin-bottom: 0.5rem;
    display: block;
}
.poll-option-btn:hover:not(:disabled) {
    border-color: #7c3aed;
    background: #faf5ff;
    transform: translateX(4px);
}
.poll-option-btn.selected {
    border-color: #7c3aed;
    background: linear-gradient(90deg, rgba(124,58,237,0.08), transparent);
    font-weight: 700;
}

/* Result Bar */
.result-bar-wrap {
    position: relative;
    background: #f1f5f9;
    border-radius: 8px;
    height: 36px;
    display: flex; align-items: center;
    overflow: hidden;
    margin-bottom: 0.5rem;
}
.result-bar-fill {
    position: absolute; left: 0; top: 0; bottom: 0;
    background: linear-gradient(90deg, #7c3aed, #a855f7);
    border-radius: 8px;
    transition: width 1s cubic-bezier(0.4,0,0.2,1);
}
.result-bar-fill.winner { background: linear-gradient(90deg, #059669, #10b981); }
.result-bar-text {
    position: relative; z-index: 1;
    padding: 0 12px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex; justify-content: space-between;
    width: 100%;
}

/* Submit btn */
.vote-btn {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 10px 28px;
    font-weight: 700;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.vote-btn:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(124,58,237,0.35); color: #fff; }
.vote-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* Create poll card */
.create-poll-card {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 20px;
    color: #fff;
    padding: 1.5rem;
}

/* Closed polls */
.closed-badge {
    background: #fee2e2; color: #dc2626;
    border-radius: 20px; padding: 3px 10px; font-size: 0.7rem; font-weight: 700;
}
.active-badge {
    background: #dcfce7; color: #16a34a;
    border-radius: 20px; padding: 3px 10px; font-size: 0.7rem; font-weight: 700;
}

/* Admin action buttons */
.poll-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}
.btn-poll-edit {
    background: #ede9fe;
    color: #7c3aed;
    border: none;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-poll-edit:hover { background: #7c3aed; color: #fff; }
.btn-poll-delete {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-poll-edit, .btn-poll-delete {
    background: #7c3aed;
    color: #fff;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 0.35rem;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: background 0.2s;
}

.btn-poll-edit:hover { background: #5b21b6; }
.btn-poll-delete:hover { background: #dc2626; }

/* Dark mode */
.dark .poll-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
.dark .poll-option-btn { background: #0f172a; border-color: rgba(255,255,255,0.1); color: #e2e8f0; }
.dark .poll-option-btn:hover:not(:disabled) { background: #1e1040; border-color: #7c3aed; }
.dark .result-bar-wrap { background: rgba(255,255,255,0.08); }
.dark .result-bar-text { color: #e2e8f0; }
</style>

<section class="poll-hero">
    <div class="container position-relative text-white text-center">
        <div style="font-size: 4rem; margin-bottom: 0.5rem;">🗳️</div>
        <h1 class="display-6 fw-black mb-2">Voting & Polling Alumni</h1>
        <p class="opacity-75 mb-4">Suaramu penting! Vote, berdebat, dan putuskan bersama 💜</p>
        @can('manage-polls')

        <button class="btn btn-light text-purple fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createPollModal" style="color: #7c3aed;">
            <i class="bi bi-plus-lg me-2"></i>Buat Polling Baru
        </button>
        @endcan
    </div>
</section>

<div class="container py-5">
    @if (isset($errors) && $errors->any())
    <div class="alert alert-danger rounded-4 mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @if (session('success'))
    <div class="alert alert-success rounded-4 mb-4">
        {{ session('success') }}
    </div>
    @endif

        @if(auth()->check())
            <div id="debug-auth" style="color:#ff6600; margin-bottom:1rem;">Logged in as {{ auth()->user()->email }} (role: {{ auth()->user()->role }})</div>
        @else
            <div id="debug-auth" style="color:#ff6600; margin-bottom:1rem;">Not logged in</div>
        @endif

        {{-- Active Polls --}}
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4"><span class="active-badge me-2">● LIVE</span>Polling Aktif</h5>

            @forelse ($activePolls as $poll)
            <div class="poll-card" id="poll-{{ $poll->id }}">
                <div class="poll-header d-flex gap-3 align-items-start">
                    <div class="poll-emoji">{{ $poll->emoji ?? '🗳️' }}</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $poll->question ?? $poll->title }}</h6>
                        @if ($poll->description)
                        <p class="text-muted small mb-1">{{ $poll->description }}</p>
                        @endif
                        <div class="d-flex gap-3 text-muted" style="font-size: 0.75rem;">
                            <span><i class="bi bi-person me-1"></i>
                                {{ ($poll->is_anonymous ?? false) ? 'Anonim' : ($poll->creator?->name ?? 'Admin') }}
                            </span>
                            <span><i class="bi bi-bar-chart me-1"></i>{{ number_format($poll->total_votes) }} suara</span>
                            @if ($poll->ends_at)
                            <span><i class="bi bi-clock me-1"></i>Sampai {{ $poll->ends_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <span class="active-badge">● LIVE</span>
    @if(auth()->check())
        <div class="poll-actions">
            <a href="{{ route('polls.edit', $poll) }}" class="btn-poll-edit" style="display:inline-block;">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
            <form method="POST" action="{{ route('polls.destroy', $poll) }}" style="display:inline;" onsubmit="return confirm('Hapus polling \"{{ $poll->question ?? $poll->title }}\"?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-poll-delete" style="display:inline-block;">
                    <i class="bi bi-trash-fill"></i> Hapus
                </button>
            </form>
        </div>
    @endif
                    </div>
                </div>
                <div class="poll-body">
                    @if ($poll?->user_voted || !auth()->check())
                    {{-- Results View --}}
                    @php $maxVotes = $poll->options->max('votes_count'); @endphp
                    @foreach ($poll->options as $opt)
                    <div class="result-bar-wrap">
                        <div class="result-bar-fill {{ $opt->votes_count == $maxVotes && $maxVotes > 0 ? 'winner' : '' }}"
                             style="width: {{ $opt->percentage }}%"></div>
                        <div class="result-bar-text">
                            <span>{{ $opt->option_emoji ?? '' }} {{ $opt->option_text }}
                                @if ($poll->user_vote_ids->contains($opt->id))
                                <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                @endif
                            </span>
                            <span class="fw-bold">{{ $opt->percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-muted small mt-2">{{ number_format($poll->total_votes) }} total suara</div>
                    @else
                    {{-- Voting View --}}
                    <form class="poll-form" data-poll="{{ $poll->id }}" data-type="{{ $poll->type ?? 'single' }}">
                        @foreach ($poll->options as $opt)
                        <label class="poll-option-btn d-flex align-items-center gap-2" for="opt-{{ $opt->id }}">
                            <input type="{{ ($poll->type ?? 'single') === 'multiple' ? 'checkbox' : 'radio' }}"
                                   name="option_{{ $poll->id }}"
                                   id="opt-{{ $opt->id }}"
                                   value="{{ $opt->id }}"
                                   style="accent-color: #7c3aed;"
                                   onchange="this.closest('label').classList.toggle('selected', this.checked)">
                            <span>{{ $opt->option_emoji ?? '' }} {{ $opt->option_text }}</span>
                        </label>
                        @endforeach

                        <div class="mt-3">
                            <button type="submit" class="vote-btn">
                                <i class="bi bi-send me-2"></i>Vote Sekarang
                            </button>
                            @if (($poll->type ?? 'single') === 'multiple')
                            <span class="text-muted small ms-3">Bisa pilih lebih dari satu</span>
                            @endif
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div style="font-size: 3rem;">🗳️</div>
                <h5 class="mt-3 text-muted">Belum ada polling aktif</h5>
                @can('manage-polls')
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#createPollModal">
                    Buat polling pertama!
                </button>
                @endcan
            </div>
            @endforelse

            {{-- Closed Polls --}}
            @if ($closedPolls->isNotEmpty())
            <h5 class="fw-bold mb-3 mt-5"><span class="closed-badge me-2">✕ SELESAI</span>Polling Selesai</h5>
            @foreach ($closedPolls as $poll)
            <div class="poll-card opacity-75">
                <div class="poll-header d-flex gap-3 align-items-start">
                    <div style="font-size: 1.8rem;">{{ $poll->emoji ?? '🗳️' }}</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $poll->question ?? $poll->title }}</h6>
                        <span class="text-muted small">{{ number_format($poll->total_votes) }} suara</span>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <span class="closed-badge">✕ SELESAI</span>
                        @can('manage-polls')
    <span id="debug-can" style="color:#0f0;">CAN OK</span>
                        <div class="poll-actions" style="display:flex; gap:0.5rem;">
                            <button class="btn-poll-edit" style="display:inline-block;background:#ffcc00;color:#000;padding:4px 8px;border-radius:4px;" onclick="openEditModal({{ $poll->id }}, {{ json_encode($poll->question ?? $poll->title) }}, {{ json_encode($poll->description) }}, {{ json_encode($poll->emoji ?? '🗳️') }}, {{ json_encode($poll->type ?? 'single') }}, {{ json_encode($poll->ends_at ? $poll->ends_at->format('Y-m-d\TH:i') : '') }}, {{ json_encode((bool)($poll->is_anonymous ?? false)) }}, {{ json_encode((bool)$poll->is_active) }}, {{ $poll->options->map(fn($o) => ['emoji' => $o->option_emoji ?? '', 'text' => $o->option_text])->toJson() }})"><i class="bi bi-pencil-fill"></i> Edit</button>
                            <button class="btn-poll-delete" style="display:inline-block;background:#ff4444;color:#fff;padding:4px 8px;border-radius:4px;" onclick="confirmDelete({{ $poll->id }}, {{ json_encode($poll->question ?? $poll->title) }})"><i class="bi bi-trash-fill"></i> Hapus</button>
                        </div>
                        @endcan
                    </div>
                </div>
                <div class="poll-body">
                    @php $maxVotes = $poll->options->max('votes_count'); @endphp
                    @foreach ($poll->options as $opt)
                    <div class="result-bar-wrap">
                        <div class="result-bar-fill {{ $opt->votes_count == $maxVotes && $maxVotes > 0 ? 'winner' : '' }}"
                             style="width: {{ $opt->percentage }}%"></div>
                        <div class="result-bar-text">
                            <span>{{ $opt->option_emoji ?? '' }} {{ $opt->option_text }}</span>
                            <span class="fw-bold">{{ $opt->percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            @can('manage-polls')
            <div class="create-poll-card mb-4">
                <h6 class="fw-bold mb-2">💡 Ide polling seru?</h6>
                <p class="small opacity-75 mb-3">Buat polling dan ajak seluruh alumni untuk vote!</p>
                <button class="btn btn-light fw-bold rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#createPollModal" style="color: #7c3aed;">
                    <i class="bi bi-plus-lg me-2"></i>Buat Polling
                </button>
            </div>
            @endcan

            <div class="rounded-4 p-4 border">
                <h6 class="fw-bold mb-3">💡 Contoh Ide Polling</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">😤 Guru paling galak tapi paling dirindukan?</li>
                    <li class="mb-2">🎉 Kelas paling rusuh angkatan berapa?</li>
                    <li class="mb-2">📍 Lokasi reuni berikutnya?</li>
                    <li class="mb-2">👕 Desain kaos reuni pilihan?</li>
                    <li class="mb-2">🍜 Kantin favorit dulu?</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Create Poll Modal --}}
@can('manage-polls')
<div class="modal fade" id="createPollModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">🗳️ Buat Polling Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('polls.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Emoji</label>
                            <input type="text" name="emoji" class="form-control rounded-3 text-center fs-4"
                                   value="🗳️" maxlength="2">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="question" class="form-control rounded-3"
                                   placeholder="Contoh: Guru paling galak di Steman?" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi (opsional)</label>
                            <input type="text" name="description" class="form-control rounded-3"
                                   placeholder="Keterangan tambahan...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe</label>
                            <select name="type" class="form-select rounded-3">
                                <option value="single">Pilihan Tunggal</option>
                                <option value="multiple">Pilihan Ganda</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berakhir (opsional)</label>
                            <input type="datetime-local" name="ends_at" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Anonim?</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="isAnon">
                                <label class="form-check-label" for="isAnon">Sembunyikan nama pembuat</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span></label>
                            <div id="optionsContainer">
                                @for ($i = 0; $i < 4; $i++)
                                <div class="d-flex gap-2 mb-2">
                                    <input type="text" name="option_emojis[]" class="form-control rounded-3 text-center"
                                           style="width: 60px; flex-shrink: 0;" placeholder="🔥" maxlength="2">
                                    <input type="text" name="options[]" class="form-control rounded-3"
                                           placeholder="Opsi {{ $i + 1 }}..." {{ $i < 2 ? 'required' : '' }}>
                                </div>
                                @endfor
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-1"
                                    onclick="addOption()">
                                <i class="bi bi-plus"></i> Tambah Opsi
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="vote-btn">
                        <i class="bi bi-send me-2"></i>Buat Polling
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Poll Modal --}}
<div class="modal fade" id="editPollModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">✏️ Edit Polling</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPollForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Emoji</label>
                            <input type="text" name="emoji" id="editEmoji" class="form-control rounded-3 text-center fs-4" maxlength="2">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="question" id="editQuestion" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi (opsional)</label>
                            <input type="text" name="description" id="editDescription" class="form-control rounded-3" placeholder="Keterangan tambahan...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe</label>
                            <select name="type" id="editType" class="form-select rounded-3">
                                <option value="single">Pilihan Tunggal</option>
                                <option value="multiple">Pilihan Ganda</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berakhir (opsional)</label>
                            <input type="datetime-local" name="ends_at" id="editEndsAt" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status & Anonim</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                                <label class="form-check-label" for="editIsActive">Polling Aktif</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="editIsAnonymous" value="1">
                                <label class="form-check-label" for="editIsAnonymous">Anonim</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilihan Jawaban <span class="text-danger">*</span></label>
                            <div id="editOptionsContainer"></div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-1"
                                    onclick="addEditOption()">
                                <i class="bi bi-plus"></i> Tambah Opsi
                            </button>
                            <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>Jumlah vote yang sudah masuk akan tetap tersimpan.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="vote-btn">
                        <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="deletePollForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endcan

@push('scripts')
<script>
let optCount = 4;
function addOption() {
    if (optCount >= 10) return;
    optCount++;
    document.getElementById('optionsContainer').insertAdjacentHTML('beforeend', `
        <div class="d-flex gap-2 mb-2">
            <input type="text" name="option_emojis[]" class="form-control rounded-3 text-center"
                   style="width:60px;flex-shrink:0;" placeholder="✨" maxlength="2">
            <input type="text" name="options[]" class="form-control rounded-3" placeholder="Opsi ${optCount}...">
        </div>
    `);
}

let editOptCount = 0;
function addEditOption(emoji = '', text = '') {
    editOptCount++;
    document.getElementById('editOptionsContainer').insertAdjacentHTML('beforeend', `
        <div class="d-flex gap-2 mb-2 edit-option-row">
            <input type="text" name="option_emojis[]" class="form-control rounded-3 text-center"
                   style="width:60px;flex-shrink:0;" placeholder="✨" maxlength="2" value="${emoji}">
            <input type="text" name="options[]" class="form-control rounded-3"
                   placeholder="Opsi..." value="${text}" ${editOptCount <= 2 ? 'required' : ''}>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-3" style="flex-shrink:0;"
                    onclick="this.closest('.edit-option-row').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    `);
}

function openEditModal(id, question, description, emoji, type, endsAt, isAnonymous, isActive, options) {
    const form = document.getElementById('editPollForm');
    form.action = `/polls/${id}`;

    document.getElementById('editEmoji').value = emoji || '🗳️';
    document.getElementById('editQuestion').value = question || '';
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editType').value = type || 'single';
    document.getElementById('editEndsAt').value = endsAt || '';
    document.getElementById('editIsActive').checked = isActive;
    document.getElementById('editIsAnonymous').checked = isAnonymous;

    const container = document.getElementById('editOptionsContainer');
    container.innerHTML = '';
    editOptCount = 0;

    if (Array.isArray(options)) {
        options.forEach(opt => {
            addEditOption(opt.emoji || '', opt.text || '');
        });
    }
    while (editOptCount < 2) {
        addEditOption('', '');
    }

    const modal = new bootstrap.Modal(document.getElementById('editPollModal'));
    modal.show();
}

function confirmDelete(id, question) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Polling?',
            html: `Polling "<strong>${question}</strong>" akan dihapus permanen.`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i>Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                submitDelete(id);
            }
        });
    } else {
        if (confirm(`Hapus polling "${question}"?`)) {
            submitDelete(id);
        }
    }
}

function submitDelete(id) {
    const form = document.getElementById('deletePollForm');
    form.action = `/polls/${id}`;
    form.submit();
}

// Handle vote submission
document.querySelectorAll('.poll-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const pollId = this.dataset.poll;
        const type   = this.dataset.type;
        const inputs = type === 'single'
            ? this.querySelectorAll('input[type=radio]:checked')
            : this.querySelectorAll('input[type=checkbox]:checked');

        if (!inputs.length) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Pilih dulu!', text: 'Pilih minimal 1 jawaban.', confirmButtonColor: '#7c3aed' });
            } else {
                alert('Pilih minimal 1 jawaban!');
            }
            return;
        }

        const optionIds = Array.from(inputs).map(i => i.value);
        const btn = this.querySelector('[type=submit]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Voting...';

        fetch(`/polls/${pollId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ option_ids: optionIds })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById(`poll-${pollId}`);
                const body = card.querySelector('.poll-body');
                const maxVotes = Math.max(...data.results.map(r => r.votes));

                body.innerHTML = data.results.map(opt => `
                    <div class="result-bar-wrap">
                        <div class="result-bar-fill ${opt.votes === maxVotes && maxVotes > 0 ? 'winner' : ''}"
                             style="width:0%" data-width="${opt.percentage}%"></div>
                        <div class="result-bar-text">
                            <span>${opt.emoji || ''} ${opt.text} ${optionIds.includes(String(opt.id)) ? '<i class="bi bi-check-circle-fill text-success ms-1"></i>' : ''}</span>
                            <span class="fw-bold">${opt.percentage}%</span>
                        </div>
                    </div>
                `).join('') + `<div class="text-muted small mt-2">${data.total_votes.toLocaleString('id-ID')} total suara</div>`;

                setTimeout(() => {
                    body.querySelectorAll('.result-bar-fill').forEach(el => {
                        el.style.width = el.dataset.width;
                    });
                }, 100);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Vote tersimpan! 🎉', timer: 1500, showConfirmButton: false });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.error });
                } else {
                    alert(data.error);
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-2"></i>Vote Sekarang';
            }
        });
    });
});

// Animate existing result bars on load
document.querySelectorAll('.result-bar-fill').forEach(el => {
    const target = el.style.width;
    el.style.width = '0%';
    setTimeout(() => { el.style.width = target; }, 300);
});
</script>
@endpush
@endsection
