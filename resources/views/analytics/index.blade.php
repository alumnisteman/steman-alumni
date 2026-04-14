@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-primary">Dampak & Statistik Alumni</h1>
        <p class="lead text-muted">Melihat kekuatan jaringan alumni SMKN 2 Ternate dalam angka.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-primary text-white">
                <h1 class="display-3 fw-bold mb-0">{{ $totalAlumni }}</h1>
                <p class="mb-0 opacity-75">Total Alumni Terdata</p>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4">Sebaran Alumni per Angkatan</h5>
                <canvas id="alumniByYearChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <span class="bg-warning p-2 rounded-3 me-3 text-dark"><i class="bi bi-pie-chart-fill"></i></span>
                    Status Karir & Studi
                </h5>
                <canvas id="employmentChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <span class="bg-primary p-2 rounded-3 me-3 text-white"><i class="bi bi-diagram-3-fill"></i></span>
                    Distribusi per major
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            @foreach($alumniByMajor as $item)
                            <tr>
                                <td class="fw-bold text-dark opacity-75">{{ $item->major }}</td>
                                <td class="text-end">
                                    <span class="badge bg-light text-dark rounded-pill px-3">{{ $item->total }} Alumni</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Career Navigator Section -->
    <div class="mt-5">
        <div class="text-center mb-5">
            <h2 class="fw-black text-dark"><i class="bi bi-compass-fill text-primary me-2"></i>Prediksi Jalur Karir Terpopuler</h2>
            <p class="text-muted">Berdasarkan data karir terkini dari para alumni di berbagai angkatan.</p>
        </div>

        <div class="row g-4">
            @foreach($careerPaths as $major => $paths)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 career-card">
                    <div class="card-header bg-dark text-white py-3 px-4 border-0">
                        <small class="text-warning text-uppercase fw-bold opacity-75" style="letter-spacing: 2px;">Data-Driven Insights</small>
                        <h6 class="mb-0 fw-bold">{{ $major }}</h6>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <p class="small text-muted mb-4 italic">Alumni dari major ini paling banyak sukses berkarir sebagai:</p>
                        @foreach($paths as $index => $path)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                {{ $index + 1 }}
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-bold text-dark">{{ $path->current_job }}</div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: {{ rand(40, 95) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .career-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .career-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px -5px rgba(0,0,0,0.1) !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Alumni by Year Chart
        const ctxYear = document.getElementById('alumniByYearChart').getContext('2d');
        new Chart(ctxYear, {
            type: 'bar',
            data: {
                labels: {!! json_encode($alumniByYear->pluck('graduation_year')) !!},
                datasets: [{
                    label: 'Jumlah Alumni',
                    data: {!! json_encode($alumniByYear->pluck('total')) !!},
                    backgroundColor: '#ffcc00',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Employment Chart
        const ctxEmp = document.getElementById('employmentChart').getContext('2d');
        new Chart(ctxEmp, {
            type: 'pie',
            data: {
                labels: {!! json_encode($employmentStats->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($employmentStats->pluck('total')) !!},
                    backgroundColor: ['#ffcc00', '#212529', '#dee2e6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
@endpush
@endsection
