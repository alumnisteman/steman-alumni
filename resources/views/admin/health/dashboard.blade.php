@extends('layouts.admin')

@section('title', 'Tren Kesehatan Alumni')

@section('admin-content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1 text-uppercase"><i class="bi bi-heart-pulse-fill text-danger me-2"></i>ANALITIK TREN KESEHATAN</h2>
            <p class="text-muted">Dasbor anonim agregat untuk memantau profil kesehatan alumni secara umum.</p>
        </div>
    </div>

    <!-- Privacy Notice -->
    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start">
        <i class="bi bi-shield-lock-fill fs-3 text-info me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Kepatuhan Privasi Data Tinggi</h5>
            <p class="mb-0 small">Data di bawah ini dikumpulkan secara anonim. Identitas individu, berat badan spesifik, dan rekaman gejala medis tetap terenkripsi penuh di database dan tidak dapat diakses dari panel ini.</p>
        </div>
    </div>

    <!-- Top Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <p class="text-muted fw-bold small mb-2 text-uppercase">Total Alumni Aktif</p>
                <h1 class="display-5 fw-black text-dark mb-0">{{ number_format($totalAlumni) }}</h1>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <p class="text-muted fw-bold small mb-2 text-uppercase">Alumni Usia 40+</p>
                <h1 class="display-5 fw-black text-danger mb-0">{{ number_format($alumniOver40) }}</h1>
                <p class="text-danger small mb-0 mt-2"><i class="bi bi-exclamation-circle-fill me-1"></i> Membutuhkan perhatian ekstra</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-4">
                <p class="text-muted fw-bold small mb-2 text-uppercase">Persentase Usia Rawan</p>
                <h1 class="display-5 fw-black text-success mb-0">{{ $percentageOver40 }}%</h1>
                <p class="text-success small mb-0 mt-2">Dari total alumni</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- BMI Trends -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi BMI</h5>
                    <p class="text-muted small">Body Mass Index Alumni</p>
                </div>
                <div class="card-body p-4">
                    @if(empty($bmiTrends))
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data kesehatan yang terkumpul.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($bmiTrends as $category => $count)
                                @php 
                                    $total = array_sum($bmiTrends);
                                    $percent = $total > 0 ? round(($count / $total) * 100) : 0;
                                    $colorClass = 'bg-secondary';
                                    if($category == 'Normal') $colorClass = 'bg-success';
                                    if($category == 'Overweight') $colorClass = 'bg-warning';
                                    if($category == 'Obese') $colorClass = 'bg-danger';
                                    if($category == 'Underweight') $colorClass = 'bg-info';
                                @endphp
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-bold">{{ $category }}</span>
                                        <span class="text-muted">{{ $count }} orang ({{ $percent }}%)</span>
                                    </div>
                                    <div class="progress rounded-pill bg-light" style="height: 10px;">
                                        <div class="progress-bar {{ $colorClass }} rounded-pill" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Trends -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark"><i class="bi bi-activity text-success me-2"></i>Level Aktivitas Fisik</h5>
                    <p class="text-muted small">Intensitas olahraga per minggu</p>
                </div>
                <div class="card-body p-4">
                    @if(empty($activityTrends))
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada data aktivitas yang terkumpul.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($activityTrends as $level => $count)
                                @php 
                                    $totalAct = array_sum($activityTrends);
                                    $percentAct = $totalAct > 0 ? round(($count / $totalAct) * 100) : 0;
                                    $colorClassAct = 'bg-secondary';
                                    if($level == 'Rendah') $colorClassAct = 'bg-danger';
                                    if($level == 'Sedang') $colorClassAct = 'bg-warning';
                                    if($level == 'Tinggi') $colorClassAct = 'bg-success';
                                @endphp
                                <div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-bold">{{ $level }}</span>
                                        <span class="text-muted">{{ $count }} orang ({{ $percentAct }}%)</span>
                                    </div>
                                    <div class="progress rounded-pill bg-light" style="height: 10px;">
                                        <div class="progress-bar {{ $colorClassAct }} rounded-pill" role="progressbar" style="width: {{ $percentAct }}%" aria-valuenow="{{ $percentAct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
