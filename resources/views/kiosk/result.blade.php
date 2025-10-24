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


    <!-- Bottom UI Elements Overlay -->
    <div class="absolute bottom-0 left-0 right-0 p-4 flex justify-between items-end">
        <!-- Daintee Logo on Left -->
        <div class="flex items-center">
            <div class="text-red-600 font-bold responsive-title" style="font-family: 'Creepster', cursive; text-shadow: 0.25vh 0.25vh 0.5vh rgba(0,0,0,0.8);">
                Daintee
            </div>
        </div>

        <!-- QR Code on Right -->
        <div class="flex flex-col items-center">
            <div id="qrCode" class="bg-white vh-p-2 rounded shadow-lg vh-mb-2" style="margin-bottom: 5vh;width: 12vh;height: 12vh;display: flex;align-items: center;justify-content: center;background-color: white;padding: 10px;">
                <!-- QR Code will be generated here -->
            </div>
            <div class="text-center text-white">
                <p class="responsive-subtitle font-semibold">Download Your Image</p>
                <p class="responsive-subtitle text-gray-300">Using the QR Code</p>
            </div>
        </div>
    </div>

    <!-- Download & Share Buttons (Only visible when accessed via QR code) -->
    <div id="qrActions" class="absolute bottom-20 left-1/2 transform -translate-x-1/2 hidden">
        <div class="bg-black bg-opacity-80 rounded-lg p-4 flex space-x-4">
            <button onclick="downloadImage()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 responsive-btn">
                📥 Download
            </button>
            <button onclick="shareImage()" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 responsive-btn">
                📤 Share
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

<script>

    // Generate QR Code for current URL
    function generateQRCode() {
        // Add QR parameter to URL for detection
        const currentUrl = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'qr=true';
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
            const size = 200; // Higher resolution for better quality
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
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.objectFit = 'contain';
            qrContainer.appendChild(canvas);


        } catch (error) {
            // Fallback: show URL as text
            qrContainer.innerHTML = `
                <div class="text-center vh-p-2" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <p class="responsive-subtitle text-gray-600 vh-mb-1">Scan:</p>
                    <p class="responsive-subtitle text-gray-800 break-all text-center">${currentUrl}</p>
                </div>
            `;
        }
    }

    // Alternative QR code generation using online service
    function generateQRCodeOnline() {
        // Add QR parameter to URL for detection
        const currentUrl = window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'qr=true';
        const qrContainer = document.getElementById('qrCode');

        // Use QR Server API as fallback
        const qrImage = document.createElement('img');
        qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(currentUrl)}`;
        qrImage.alt = 'QR Code';
        qrImage.style.width = '100%';
        qrImage.style.height = '100%';
        qrImage.style.objectFit = 'contain';

        qrImage.onload = function() {
            qrContainer.innerHTML = '';
            qrContainer.appendChild(qrImage);
        };

        qrImage.onerror = function() {
            // Show fallback text
            qrContainer.innerHTML = `
                <div class="text-center vh-p-2" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <p class="responsive-subtitle text-gray-600 vh-mb-1">Scan:</p>
                    <p class="responsive-subtitle text-gray-800 break-all text-center">${currentUrl}</p>
                </div>
            `;
        };
    }


    // Check if accessed via QR code
    function checkQRCodeAccess() {
        const urlParams = new URLSearchParams(window.location.search);
        const referrer = document.referrer;

        // Check for QR code parameters or referrer
        if (urlParams.get('qr') === 'true' ||
            urlParams.get('from') === 'qr' ||
            referrer.includes('qr') ||
            sessionStorage.getItem('qrAccessed') === 'true') {

            // Show download and share buttons
            $('#qrActions').removeClass('hidden').addClass('fade-in');

            // Mark as QR accessed
            sessionStorage.setItem('qrAccessed', 'true');

        }
    }

    // Download image function
    function downloadImage() {
        const imageUrl = "{{ Storage::url($session->processed_image_path) }}";
        const link = document.createElement('a');
        link.href = imageUrl;
        link.download = 'dracula-transformation.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Show success message
        showMessage('Image downloaded successfully! 📥');
    }

    // Share image function
    function shareImage() {
        const imageUrl = "{{ Storage::url($session->processed_image_path) }}";
        const shareText = "🧛‍♂️ Check out my amazing Dracula transformation! Happy Halloween! 🦇";
        const shareUrl = window.location.href;

        if (navigator.share) {
            // Use native sharing if available
            navigator.share({
                title: 'My Dracula Transformation',
                text: shareText,
                url: shareUrl
            }).then(() => {
                showMessage('Shared successfully! 📤');
            }).catch((err) => {
                fallbackShare();
            });
        } else {
            // Fallback to copy link
            fallbackShare();
        }
    }

    // Fallback share function
    function fallbackShare() {
        const shareText = "🧛‍♂️ Check out my amazing Dracula transformation! Happy Halloween! 🦇";
        const shareUrl = window.location.href;
        const fullText = shareText + ' ' + shareUrl;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(fullText).then(() => {
                showMessage('Link copied to clipboard! 📋');
            }).catch(() => {
                showMessage('Please copy the link manually: ' + shareUrl);
            });
        } else {
            showMessage('Please copy the link manually: ' + shareUrl);
        }
    }

    // Show temporary message
    function showMessage(message) {
        const messageDiv = $('<div>')
            .text(message)
            .css({
                'position': 'fixed',
                'top': '50%',
                'left': '50%',
                'transform': 'translate(-50%, -50%)',
                'background': 'rgba(0, 0, 0, 0.9)',
                'color': 'white',
                'padding': '1.875vh 3.125vh',
                'border-radius': '1vh',
                'z-index': '10000',
                'font-size': '2vh',
                'font-weight': 'bold'
            });

        $('body').append(messageDiv);

        setTimeout(() => {
            messageDiv.fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Initialize when page loads
    $(document).ready(function() {
        // Try to generate QR code locally first
        try {
            generateQRCode();
        } catch (error) {
            generateQRCodeOnline();
        }

        // Add fade-in effect to the result image
        setTimeout(function() {
            $('#resultImage').addClass('fade-in');
        }, 500);

        // Check for QR code access
        checkQRCodeAccess();
    });
</script>
@endsection

@php
$currentStep = 6;
@endphp
