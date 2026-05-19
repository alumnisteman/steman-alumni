@extends('layouts.app')

@section('content')
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
        @auth
        <button class="btn btn-light text-purple fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createPollModal" style="color: #7c3aed;">
            <i class="bi bi-plus-lg me-2"></i>Buat Polling Baru
        </button>
        @endauth
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">

        {{-- Active Polls --}}
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4"><span class="active-badge me-2">● LIVE</span>Polling Aktif</h5>

            @forelse ($activePolls as $poll)
            <div class="poll-card" id="poll-{{ $poll->id }}">
                <div class="poll-header d-flex gap-3 align-items-start">
                    <div class="poll-emoji">{{ $poll->emoji }}</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $poll->question }}</h6>
                        @if ($poll->description)
                        <p class="text-muted small mb-1">{{ $poll->description }}</p>
                        @endif
                        <div class="d-flex gap-3 text-muted" style="font-size: 0.75rem;">
                            <span><i class="bi bi-person me-1"></i>
                                {{ $poll->is_anonymous ? 'Anonim' : $poll->creator->name }}
                            </span>
                            <span><i class="bi bi-bar-chart me-1"></i>{{ number_format($poll->total_votes) }} suara</span>
                            @if ($poll->ends_at)
                            <span><i class="bi bi-clock me-1"></i>Sampai {{ $poll->ends_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="active-badge">● LIVE</span>
                </div>
                <div class="poll-body">
                    @if ($poll->user_voted || !auth()->check())
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
                    <form class="poll-form" data-poll="{{ $poll->id }}" data-type="{{ $poll->type }}">
                        @foreach ($poll->options as $opt)
                        <label class="poll-option-btn d-flex align-items-center gap-2" for="opt-{{ $opt->id }}">
                            <input type="{{ $poll->type === 'multiple' ? 'checkbox' : 'radio' }}"
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
                            @if ($poll->type === 'multiple')
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
                @auth
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#createPollModal">
                    Buat polling pertama!
                </button>
                @endauth
            </div>
            @endforelse

            {{-- Closed Polls --}}
            @if ($closedPolls->isNotEmpty())
            <h5 class="fw-bold mb-3 mt-5"><span class="closed-badge me-2">✕ SELESAI</span>Polling Selesai</h5>
            @foreach ($closedPolls as $poll)
            <div class="poll-card opacity-75">
                <div class="poll-header d-flex gap-3 align-items-start">
                    <div style="font-size: 1.8rem;">{{ $poll->emoji }}</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $poll->question }}</h6>
                        <span class="text-muted small">{{ number_format($poll->total_votes) }} suara</span>
                    </div>
                    <span class="closed-badge">✕ SELESAI</span>
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
            @auth
            <div class="create-poll-card mb-4">
                <h6 class="fw-bold mb-2">💡 Ide polling seru?</h6>
                <p class="small opacity-75 mb-3">Buat polling dan ajak seluruh alumni untuk vote!</p>
                <button class="btn btn-light fw-bold rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#createPollModal" style="color: #7c3aed;">
                    <i class="bi bi-plus-lg me-2"></i>Buat Polling
                </button>
            </div>
            @endauth

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
@auth
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
@endauth

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
            Swal.fire({ icon: 'warning', title: 'Pilih dulu!', text: 'Pilih minimal 1 jawaban.', confirmButtonColor: '#7c3aed' });
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
                // Replace with results
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

                // Animate bars
                setTimeout(() => {
                    body.querySelectorAll('.result-bar-fill').forEach(el => {
                        el.style.width = el.dataset.width;
                    });
                }, 100);

                Swal.fire({ icon: 'success', title: 'Vote tersimpan! 🎉', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.error });
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
