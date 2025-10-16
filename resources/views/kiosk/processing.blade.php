@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('05/BG.png') }}" alt="Background" class="w-full h-full object-cover">
    </div>
    
    <!-- Content Overlay -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
        <!-- Dracula Logo -->
        <div class="mb-8">
            <img src="{{ asset('05/Dracula Logo.png') }}" alt="Dracula Logo" class="mx-auto max-w-xs">
        </div>
        
        <!-- Processing Content -->
        <div class="mb-8 text-center text-white">
            <div class="mb-6">
                <div class="loading-spinner mx-auto mb-6"></div>
                <div class="space-y-2">
                    <p class="text-lg font-semibold text-yellow-400" id="statusText">Analyzing your photo...</p>
                    <p class="text-sm text-gray-400" id="progressText">Please wait while we work our magic</p>
                </div>
            </div>
            
            <div class="mb-8">
                <div class="bg-black bg-opacity-50 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-red-400 mb-4">🦇 Transformation Progress</h3>
                    <div class="space-y-3 text-left">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-200">Photo captured</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-200">Photo confirmed</span>
                        </div>
                        <div class="flex items-center" id="processingStep">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3 pulse-animation"></div>
                            <span class="text-sm text-gray-200">AI processing...</span>
                        </div>
                        <div class="flex items-center" id="finalStep">
                            <div class="w-4 h-4 bg-gray-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-400">Finalizing transformation</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-sm text-gray-400">
                <p>This may take 30-60 seconds</p>
                <p>Please do not close this screen</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-auto">
            <img src="{{ asset('05/Footer.png') }}" alt="Footer" class="mx-auto">
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let statusMessages = [
        'Analyzing your photo...',
        'Detecting facial features...',
        'Applying vampire transformation...',
        'Adding spooky effects...',
        'Finalizing your Dracula look...'
    ];
    
    let progressMessages = [
        'Please wait while we work our magic',
        'Our AI is studying your features',
        'Transforming you into Count Dracula',
        'Adding the perfect vampire touches',
        'Almost ready for your big reveal!'
    ];
    
    let currentMessageIndex = 0;
    let checkInterval;
    
    function updateStatus() {
        const statusText = document.getElementById('statusText');
        const progressText = document.getElementById('progressText');
        
        if (currentMessageIndex < statusMessages.length) {
            statusText.textContent = statusMessages[currentMessageIndex];
            progressText.textContent = progressMessages[currentMessageIndex];
            currentMessageIndex++;
        }
    }
    
    function checkProcessingStatus() {
        $.ajax({
            url: "{{ route('kiosk.status', $session->session_id) }}",
            method: 'GET',
            success: function(response) {
                console.log('Status check response:', response);
                
                if (response.completed) {
                    // Processing complete, redirect to result
                    console.log('Processing completed, redirecting to result...');
                    window.location.href = response.redirect;
                } else if (response.status === 'failed') {
                    console.log('Processing failed');
                    alert('Transformation failed. Please try again.');
                    window.location.href = "{{ route('kiosk.camera', $session->session_id) }}";
                } else if (response.status === 'processing') {
                    console.log('Still processing...');
                    // Update UI to show processing status
                    $('#statusText').text('AI is transforming your photo...');
                    $('#progressText').text('This may take 30-60 seconds');
                }
                // If still processing, continue checking
            },
            error: function(xhr, status, error) {
                console.log('Error checking status:', error, 'Will retry...');
            }
        });
    }
    
    // Start status updates
    $(document).ready(function() {
        // Update status messages every 8 seconds
        setInterval(updateStatus, 8000);
        
        // Check processing status every 3 seconds
        checkInterval = setInterval(checkProcessingStatus, 3000);
        
        // Initial status check
        setTimeout(checkProcessingStatus, 2000);
        
        // Update progress steps
        setTimeout(function() {
            $('#processingStep .w-4').removeClass('bg-yellow-500 pulse-animation').addClass('bg-green-500');
            $('#processingStep span').text('AI processing complete');
            $('#finalStep .w-4').removeClass('bg-gray-500').addClass('bg-yellow-500 pulse-animation');
            $('#finalStep span').removeClass('text-gray-400').addClass('text-gray-200');
        }, 15000);
    });
    
    // Cleanup interval when leaving page
    $(window).on('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });
</script>
@endsection

@php
$currentStep = 5;
@endphp
