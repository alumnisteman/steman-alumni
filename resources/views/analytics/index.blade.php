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
                <h5 class="fw-bold mb-4">Status Karir & Studi</h5>
                <canvas id="employmentChart" style="max-height: 300px;"></canvas>
                <div class="mt-4 small text-muted">
                    * Data berdasarkan pembaruan profil mandiri oleh alumni.
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-4">Distribusi per Jurusan</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            @foreach($alumniByMajor as $item)
                            <tr>
                                <td class="fw-bold">{{ $item->jurusan }}</td>
                                <td class="text-end">
                                    <span class="badge bg-light text-dark rounded-pill">{{ $item->total }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Alumni by Year Chart
        const ctxYear = document.getElementById('alumniByYearChart').getContext('2d');
        new Chart(ctxYear, {
            type: 'bar',
            data: {
                labels: {!! json_encode($alumniByYear->pluck('tahun_lulus')) !!},
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
