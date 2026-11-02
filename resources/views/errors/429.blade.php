@extends('errors.layout')

@section('title', 'Too Many Request')
@section('code', '429')
@section('heading', 'Too Many Request')

@section('message')
    You have sent too many requests in a short period of time. Please wait a moment before trying again.
@endsection

@section('detail')
    <p class="detail countdown">Try again <span id="timer">60</span> second</p>
@endsection

@section('scripts')
    <script>
        let seconds = 60;
        const timer = document.getElementById('timer');
        const interval = setInterval(() => {
            seconds--;
            timer.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.reload();
            }
        }, 1000);
    </script>
@endsection
