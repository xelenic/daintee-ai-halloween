@extends('kiosk.layout')

@section('content')
<div class="text-center text-white">
    <div class="mb-8">
        <h1 class="text-4xl font-bold spooky-text mb-4 pulse-animation">🧛‍♂️</h1>
        <h2 class="text-3xl font-bold spooky-text mb-2">HALLOWEEN</h2>
        <h3 class="text-2xl font-bold text-yellow-400 mb-6">DRACULA TRANSFORMATION</h3>
    </div>
    
    <div class="mb-8">
        <p class="text-lg mb-4 text-gray-200">Transform yourself into Count Dracula!</p>
        <p class="text-sm text-gray-300 mb-8">Take a photo and watch the magic happen</p>
    </div>
    
    <div class="mb-8">
        <div class="bg-black bg-opacity-50 rounded-lg p-6 mb-6">
            <h4 class="text-xl font-bold text-yellow-400 mb-4">How it works:</h4>
            <div class="text-left space-y-2 text-sm text-gray-200">
                <div class="flex items-center">
                    <span class="text-red-500 mr-2">1.</span>
                    <span>Enter your phone number</span>
                </div>
                <div class="flex items-center">
                    <span class="text-red-500 mr-2">2.</span>
                    <span>Take your photo</span>
                </div>
                <div class="flex items-center">
                    <span class="text-red-500 mr-2">3.</span>
                    <span>Get your Dracula transformation!</span>
                </div>
            </div>
        </div>
    </div>
    
    <button onclick="startExperience()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-lg glow-effect transition-all duration-300 transform hover:scale-105">
        START TRANSFORMATION
    </button>
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
