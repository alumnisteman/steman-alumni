@extends('layouts.app')

@section('title', 'Error')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="display-4 text-danger">Something went wrong.</h1>
        <p class="lead">Please try again later or contact support.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</div>
@endsection
