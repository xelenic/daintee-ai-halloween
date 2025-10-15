@extends('kiosk.layout')

@section('content')
<div class="camera-container">
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display: none;"></canvas>
    
    <div class="camera-overlay">
        <div class="text-center text-white">
            <div class="mb-6">
                <h2 class="text-2xl font-bold spooky-text mb-2">📸</h2>
                <p class="text-lg">Position yourself in the frame</p>
                <p class="text-sm text-gray-300">Look directly at the camera</p>
            </div>
            
            <div class="capture-button" onclick="capturePhoto()">
                <span>📷</span>
            </div>
            
            <div class="mt-6">
                <button 
                    onclick="goBack()"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300"
                >
                    ← Back
                </button>
            </div>
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
            console.error('Error accessing camera:', err);
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
