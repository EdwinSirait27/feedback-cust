@extends('errors.layout')

@section('title', 'Under Maintenance')
@section('code', '503')
@section('heading', 'Maintenance')

@section('message')
    Our system is currently under maintenance to provide a better experience.
@endsection

@section('detail')
    <p class="detail">We'll be back soon. Thank you for your patience.</p>
@endsection

@section('scripts')
    <script>
        // Auto refresh setiap 30 detik saat maintenance
        setTimeout(() => window.location.reload(), 30000);
    </script>
@endsection
