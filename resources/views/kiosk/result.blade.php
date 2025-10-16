@extends('kiosk.layout')

@section('content')
<div class="relative w-full h-full">
    <!-- Full Screen Processed Image -->
    @if($session->processed_image_path)
        <img 
            src="{{ Storage::url($session->processed_image_path) }}" 
            alt="Your Dracula transformation" 
            class="absolute inset-0 w-full h-full object-cover"
            id="resultImage"
        >
    @else
        <div class="absolute inset-0 w-full h-full bg-gray-800 flex items-center justify-center">
            <p class="text-gray-400">Image processing...</p>
        </div>
    @endif
    
    <!-- QR Code Section Overlay at Bottom -->
    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-80 p-6 flex flex-col items-center">
        <!-- QR Code -->
        <div class="mb-4">
            <div id="qrCode" class="bg-white p-4 rounded-lg shadow-lg">
                <!-- QR Code will be generated here -->
            </div>
        </div>
        
        <!-- QR Code Text -->
        <div class="text-center text-white">
            <p class="text-sm font-semibold mb-1">Download Your Image</p>
            <p class="text-xs text-gray-300">Using the QR Code</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-6 flex space-x-4">
            <button 
                onclick="startNew()" 
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 text-sm"
            >
                🔄 Transform Again
            </button>
            
            <button 
                onclick="goHome()"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 text-sm"
            >
                🏠 Home
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

<script>
    function startNew() {
        if (confirm('Start a new transformation?')) {
            window.location.href = "{{ route('kiosk.new') }}";
        }
    }
    
    function goHome() {
        window.location.href = "{{ route('kiosk.welcome') }}";
    }
    
    // Generate QR Code for current URL
    function generateQRCode() {
        const currentUrl = window.location.href;
        const qrContainer = document.getElementById('qrCode');
        
        try {
            // Clear any existing content
            qrContainer.innerHTML = '';
            
            // Create QR code using qrcode-generator library
            const qr = qrcode(0, 'M');
            qr.addData(currentUrl);
            qr.make();
            
            // Create canvas element
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const size = 150;
            const cellSize = size / qr.getModuleCount();
            
            canvas.width = size;
            canvas.height = size;
            
            // Fill white background
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, size, size);
            
            // Draw QR code
            ctx.fillStyle = '#000000';
            for (let row = 0; row < qr.getModuleCount(); row++) {
                for (let col = 0; col < qr.getModuleCount(); col++) {
                    if (qr.isDark(row, col)) {
                        ctx.fillRect(col * cellSize, row * cellSize, cellSize, cellSize);
                    }
                }
            }
            
            // Add canvas to container
            qrContainer.appendChild(canvas);
            
            console.log('QR Code generated successfully');
            
        } catch (error) {
            console.error('QR Code generation failed:', error);
            // Fallback: show URL as text
            qrContainer.innerHTML = `
                <div class="text-center p-4" style="width: 150px; height: 150px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <p class="text-xs text-gray-600 mb-2">Scan to download:</p>
                    <p class="text-xs text-gray-800 break-all text-center">${currentUrl}</p>
                </div>
            `;
        }
    }
    
    // Alternative QR code generation using online service
    function generateQRCodeOnline() {
        const currentUrl = window.location.href;
        const qrContainer = document.getElementById('qrCode');
        
        // Use QR Server API as fallback
        const qrImage = document.createElement('img');
        qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(currentUrl)}`;
        qrImage.alt = 'QR Code';
        qrImage.style.width = '150px';
        qrImage.style.height = '150px';
        
        qrImage.onload = function() {
            qrContainer.innerHTML = '';
            qrContainer.appendChild(qrImage);
            console.log('QR Code loaded from online service');
        };
        
        qrImage.onerror = function() {
            console.error('Online QR service failed');
            // Show fallback text
            qrContainer.innerHTML = `
                <div class="text-center p-4" style="width: 150px; height: 150px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <p class="text-xs text-gray-600 mb-2">Scan to download:</p>
                    <p class="text-xs text-gray-800 break-all text-center">${currentUrl}</p>
                </div>
            `;
        };
    }
    
    // Initialize when page loads
    $(document).ready(function() {
        // Try to generate QR code locally first
        try {
            generateQRCode();
        } catch (error) {
            console.log('Local QR generation failed, trying online service...');
            generateQRCodeOnline();
        }
        
        // Add fade-in effect to the result image
        setTimeout(function() {
            $('#resultImage').addClass('fade-in');
        }, 500);
    });
</script>
@endsection

@php
$currentStep = 6;
@endphp
