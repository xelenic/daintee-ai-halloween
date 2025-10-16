@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('01/BG.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    <!-- Content Overlay -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
        <!-- Dracula Logo -->
        <div class="mb-8">
            <img src="{{ asset('01/Dracula Logo.png') }}" alt="Dracula Logo" class="mx-auto max-w-xs" style="margin-top: 33vh;">
        </div>

        <!-- Continue Button -->
        <div class="mt-auto mb-8">
            <button onclick="startExperience()" class="transition-all duration-300 transform hover:scale-105">
                <img src="{{ asset('01/Button_Continue.png') }}" alt="Continue" class="mx-auto" style="height: 8vh;">
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

    // Auto-start after 10 seconds if no interaction
    setTimeout(function() {
        if (confirm('Ready to transform into Dracula?')) {
            startExperience();
        }
    }, 10000);
</script>
@endsection

@php
$currentStep = 1;
@endphp
