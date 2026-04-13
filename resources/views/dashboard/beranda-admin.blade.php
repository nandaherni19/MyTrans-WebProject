@extends('layouts.admin')
@section('title', 'Dashboard')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@endpush

@section('content')

 <section class="hero">
        <img src="{{ asset('img/hero-bus.png') }}" alt="Bus Hero" class="hero-bg">

        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1>Jelajahi Indonesia Bersama Kami</h1>
            <p>
                Temukan paket wisata terbaik dengan harga terjangkau dan
                fasilitas lengkap
            </p>
        </div>
    </section>


@endsection
@push('scripts')
<script>

</script>
@endpush