@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full content-container">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('01/BG.png') }}" alt="Background" class="responsive-img w-full h-full object-cover">
    </div>

    <!-- Content Overlay -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full">
        <!-- Dracula Logo -->
        <div class="mb-8">
            <img src="{{ asset('01/Dracula Logo.png') }}" alt="Dracula Logo" class="responsive-img mx-auto" style="max-width: 80%; margin-top: 20vh;">
        </div>

        <!-- Continue Button -->
        <div class="mt-auto mb-8">
            <button onclick="startExperience()" class="responsive-btn transition-all duration-300 transform hover:scale-105">
                <img src="{{ asset('01/Button_Continue.png') }}" alt="Continue" class="responsive-img mx-auto" style="height: 8vh;">
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function startExperience() {
        window.location.href = "{{ route('kiosk.phone') }}";
    }
</script>
@endsection

@php
$currentStep = 1;
@endphp
