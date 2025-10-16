@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full flex flex-col">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('04/BG.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    <!-- Header with Dracula Logo -->
    <div class="relative z-10 flex-1 flex items-center justify-center">
        <div class="mb-8">
            <img src="{{ asset('04/Dracula Logo.png') }}" alt="Dracula Logo" class="mx-auto max-w-xs" style="margin-top: 6vh;height: 12vh;">
        </div>
    </div>

    <!-- Photo Preview Frame Container -->
    <div class="relative z-10 flex-1 flex items-center justify-center px-8">
        <div class="relative">
            <!-- White Frame with Red Border containing the photo -->
            <div class="bg-white border-2 border-red-600 rounded-lg shadow-2xl" style="width: 280px;height: 260px;border-radius: 5vh;">
                <img
                    src="{{ Storage::url($session->original_image_path) }}"
                    alt="Your photo"
                    class="w-full h-full object-cover rounded"
                    style="border-radius: 5vh;"
                    id="previewImage"
                >
            </div>
        </div>
    </div>

    <!-- Controls Section -->
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center px-8">
        <!-- RETAKE Button -->
        <div class="mb-4">
            <button
                onclick="retakePhoto()"
                id="retakeBtn"
                class="transition-all duration-300 transform hover:scale-105"
            >
                <img src="{{ asset('04/Button_Retake.png') }}" alt="Retake Photo" class="mx-auto" style="height: 6vh;">
            </button>
        </div>

        <!-- NEXT Button -->
        <div class="mb-6">
            <button
                onclick="confirmPhoto()"
                id="confirmBtn"
                class="transition-all duration-300 transform hover:scale-105"
            >
                <img src="{{ asset('04/Button_Next.png') }}" alt="Next" class="mx-auto" style="height: 6vh;">
            </button>
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
