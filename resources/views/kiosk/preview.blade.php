@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('04/BG.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>
    
    <!-- Content Overlay -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
        <!-- Dracula Logo -->
        <div class="mb-6">
            <img src="{{ asset('04/Dracula Logo.png') }}" alt="Dracula Logo" class="mx-auto max-w-xs">
        </div>
        
        <!-- Photo Preview with Frame -->
        <div class="mb-6 relative">
            <img src="{{ asset('04/P_Frame.png') }}" alt="Photo Frame" class="mx-auto max-w-xs">
            <div class="absolute inset-0 flex items-center justify-center">
                <img 
                    src="{{ Storage::url($session->original_image_path) }}" 
                    alt="Your photo" 
                    class="w-48 h-48 object-cover rounded-lg"
                    id="previewImage"
                >
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="space-y-4 mb-8">
            <button 
                onclick="confirmPhoto()" 
                id="confirmBtn"
                class="w-full transition-all duration-300 transform hover:scale-105"
            >
                <img src="{{ asset('04/Button_Next.png') }}" alt="Yes, Transform Me!" class="mx-auto">
            </button>
            
            <button 
                onclick="retakePhoto()" 
                id="retakeBtn"
                class="w-full transition-all duration-300 transform hover:scale-105"
            >
                <img src="{{ asset('04/Button_Retake.png') }}" alt="Retake Photo" class="mx-auto">
            </button>
            
            <button 
                onclick="goBack()"
                class="text-gray-400 hover:text-white transition-colors duration-300"
            >
                ← Back to Camera
            </button>
        </div>
        
        <!-- Footer -->
        <div class="mt-auto">
            <img src="{{ asset('04/Footer.png') }}" alt="Footer" class="mx-auto">
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
    <div class="text-center text-white">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p id="loadingText">Processing...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmPhoto() {
        $('#loadingOverlay').removeClass('hidden');
        $('#loadingText').text('Starting transformation...');
        $('#confirmBtn').prop('disabled', true);
        $('#retakeBtn').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('kiosk.photo.confirm', $session->session_id) }}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error confirming photo. Please try again.');
                    $('#loadingOverlay').addClass('hidden');
                    $('#confirmBtn').prop('disabled', false);
                    $('#retakeBtn').prop('disabled', false);
                }
            },
            error: function() {
                alert('Error confirming photo. Please try again.');
                $('#loadingOverlay').addClass('hidden');
                $('#confirmBtn').prop('disabled', false);
                $('#retakeBtn').prop('disabled', false);
            }
        });
    }
    
    function retakePhoto() {
        if (confirm('Are you sure you want to retake the photo?')) {
            $('#loadingOverlay').removeClass('hidden');
            $('#loadingText').text('Preparing camera...');
            $('#confirmBtn').prop('disabled', true);
            $('#retakeBtn').prop('disabled', true);
            
            $.ajax({
                url: "{{ route('kiosk.retake', $session->session_id) }}",
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else {
                        alert('Error retaking photo. Please try again.');
                        $('#loadingOverlay').addClass('hidden');
                        $('#confirmBtn').prop('disabled', false);
                        $('#retakeBtn').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Error retaking photo. Please try again.');
                    $('#loadingOverlay').addClass('hidden');
                    $('#confirmBtn').prop('disabled', false);
                    $('#retakeBtn').prop('disabled', false);
                }
            });
        }
    }
    
    function goBack() {
        window.location.href = "{{ route('kiosk.camera', $session->session_id) }}";
    }
    
    // Add some visual effects to the preview image
    $(document).ready(function() {
        $('#previewImage').on('load', function() {
            $(this).addClass('fade-in');
        });
    });
</script>
@endsection

@php
$currentStep = 4;
@endphp
