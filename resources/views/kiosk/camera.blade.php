@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full flex flex-col content-container">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('03/BG.png') }}" alt="Background" class="responsive-img w-full h-full object-cover">
    </div>

        <!-- Header with Daintee Logo -->
        <div class="relative z-10 flex-1 flex items-center justify-center">
            <div class="mb-8">
                <img src="{{ asset('01/Dracula Logo.png') }}" alt="Dracula Logo" class="responsive-img mx-auto" style="max-width: 60%; margin-top: 6vh; height: 12vh;">
            </div>
        </div>

        <!-- Camera Frame Container -->
        <div class="relative z-10 flex-1 flex items-center justify-center px-8">
            <div class="relative">
                <!-- White Frame with Red Border -->
                <div class="bg-white border-2 border-red-600 rounded-lg p-4 shadow-2xl responsive-img" style="width: 40vh; height: 35vh; padding: 0; margin-bottom: 5vh; border-radius: 5vh;">
                    <!-- Video fills the frame -->
                    <video id="video" autoplay playsinline class="responsive-img w-full h-full object-cover rounded" style="border-radius: 5vh;"></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="relative z-10 flex-1 flex flex-col items-center justify-center px-8">
            <!-- Take Snap Button -->
            <div class="mb-6">
                <button onclick="capturePhoto()" class="responsive-btn transition-all duration-300 transform hover:scale-105">
                    <img src="{{ asset('03/Button_Snap.png') }}" alt="Snap Photo" class="responsive-img mx-auto" style="height: 8vh;">
                </button>
            </div>

        <!-- Back Button -->
        <div class="mb-4">
            <button
                onclick="goBack()"
                class="text-gray-300 hover:text-white transition-colors duration-300 text-sm"
            >
                ← Back
            </button>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
    <div class="text-center text-white">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p>Processing your photo...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let stream = null;
    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');
    let context = canvas.getContext('2d');

    // Initialize camera
    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                }
            });
            video.srcObject = stream;
        } catch (err) {
            alert('Unable to access camera. Please check permissions.');
        }
    }

    function capturePhoto() {
        if (!stream) {
            alert('Camera not ready. Please wait.');
            return;
        }

        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw the video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            uploadPhoto(blob);
        }, 'image/jpeg', 0.8);
    }

    function uploadPhoto(blob) {
        const formData = new FormData();
        formData.append('photo', blob, 'photo.jpg');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show loading
        $('#loadingOverlay').removeClass('hidden');

        $.ajax({
            url: "{{ route('kiosk.photo.process', $session->session_id) }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error capturing photo. Please try again.');
                    $('#loadingOverlay').addClass('hidden');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error capturing photo. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.photo) {
                        errorMessage = errors.photo[0];
                    }
                }

                alert(errorMessage);
                $('#loadingOverlay').addClass('hidden');
            }
        });
    }

    function goBack() {
        // Stop camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        window.location.href = "{{ route('kiosk.phone') }}";
    }

    // Initialize camera when page loads
    $(document).ready(function() {
        initCamera();
    });

    // Cleanup when leaving page
    $(window).on('beforeunload', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endsection

@php
$currentStep = 3;
@endphp
