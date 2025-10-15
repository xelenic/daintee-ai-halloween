@extends('kiosk.layout')

@section('content')
<div class="text-center text-white w-full">
    <div class="mb-6">
        <h2 class="text-2xl font-bold spooky-text mb-2">📸</h2>
        <h3 class="text-xl font-bold text-yellow-400 mb-2">PHOTO PREVIEW</h3>
        <p class="text-gray-300">Is this photo good for your transformation?</p>
    </div>
    
    <div class="mb-6">
        <div class="bg-black bg-opacity-50 rounded-lg p-4 mx-auto max-w-xs">
            <img 
                src="{{ Storage::url($session->original_image_path) }}" 
                alt="Your photo" 
                class="w-full h-64 object-cover rounded-lg"
                id="previewImage"
            >
        </div>
    </div>
    
    <div class="space-y-4">
        <button 
            onclick="confirmPhoto()" 
            id="confirmBtn"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg glow-effect transition-all duration-300 transform hover:scale-105"
        >
            ✅ YES, TRANSFORM ME!
        </button>
        
        <button 
            onclick="retakePhoto()" 
            id="retakeBtn"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105"
        >
            🔄 RETAKE PHOTO
        </button>
        
        <button 
            onclick="goBack()"
            class="text-gray-400 hover:text-white transition-colors duration-300"
        >
            ← Back to Camera
        </button>
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
