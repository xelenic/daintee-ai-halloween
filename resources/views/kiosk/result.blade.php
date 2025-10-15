@extends('kiosk.layout')

@section('content')
<div class="text-center text-white w-full">
    <div class="mb-6">
        <h1 class="text-4xl font-bold spooky-text mb-4 pulse-animation">🧛‍♂️</h1>
        <h2 class="text-2xl font-bold spooky-text mb-2">TRANSFORMATION COMPLETE!</h2>
        <p class="text-gray-300">Your Dracula transformation is ready</p>
    </div>
    
    <div class="mb-6">
        <div class="bg-black bg-opacity-50 rounded-lg p-4 mx-auto max-w-xs">
            @if($session->processed_image_path)
                <img 
                    src="{{ Storage::url($session->processed_image_path) }}" 
                    alt="Your Dracula transformation" 
                    class="w-full h-64 object-cover rounded-lg glow-effect"
                    id="resultImage"
                >
            @else
                <div class="w-full h-64 bg-gray-800 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Image processing...</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mb-6">
        <div class="bg-black bg-opacity-50 rounded-lg p-4">
            <h3 class="text-lg font-bold text-red-400 mb-2">🦇 Congratulations!</h3>
            <p class="text-sm text-gray-200 mb-2">You've been transformed into Count Dracula!</p>
            <p class="text-xs text-gray-400">Your transformation has been sent to: {{ $session->phone_number }}</p>
        </div>
    </div>
    
    <div class="space-y-4">
        <button 
            onclick="shareResult()" 
            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg glow-effect transition-all duration-300 transform hover:scale-105"
        >
            📱 SHARE YOUR TRANSFORMATION
        </button>
        
        <button 
            onclick="startNew()" 
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105"
        >
            🔄 TRANSFORM AGAIN
        </button>
        
        <button 
            onclick="goHome()"
            class="text-gray-400 hover:text-white transition-colors duration-300"
        >
            🏠 Back to Home
        </button>
    </div>
    
    <!-- Share modal -->
    <div id="shareModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-gray-800 rounded-lg p-6 mx-4 max-w-sm w-full">
            <h3 class="text-xl font-bold text-white mb-4">Share Your Transformation</h3>
            <div class="space-y-3">
                <button onclick="shareWhatsApp()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg">
                    📱 WhatsApp
                </button>
                <button onclick="shareFacebook()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
                    📘 Facebook
                </button>
                <button onclick="shareTwitter()" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg">
                    🐦 Twitter
                </button>
                <button onclick="copyLink()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg">
                    📋 Copy Link
                </button>
            </div>
            <button onclick="closeShareModal()" class="w-full mt-4 text-gray-400 hover:text-white">
                Cancel
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function shareResult() {
        $('#shareModal').removeClass('hidden');
    }
    
    function closeShareModal() {
        $('#shareModal').addClass('hidden');
    }
    
    function shareWhatsApp() {
        const text = "🧛‍♂️ Check out my Dracula transformation! Happy Halloween! 🦇";
        const url = encodeURIComponent(window.location.href);
        window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
        closeShareModal();
    }
    
    function shareFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        closeShareModal();
    }
    
    function shareTwitter() {
        const text = "🧛‍♂️ Check out my Dracula transformation! Happy Halloween! 🦇";
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${url}`, '_blank');
        closeShareModal();
    }
    
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
            closeShareModal();
        }).catch(function() {
            alert('Unable to copy link. Please copy manually: ' + window.location.href);
            closeShareModal();
        });
    }
    
    function startNew() {
        if (confirm('Start a new transformation?')) {
            window.location.href = "{{ route('kiosk.new') }}";
        }
    }
    
    function goHome() {
        window.location.href = "{{ route('kiosk.welcome') }}";
    }
    
    // Add celebration effects
    $(document).ready(function() {
        // Add confetti effect (simple version)
        setTimeout(function() {
            for (let i = 0; i < 20; i++) {
                createConfetti();
            }
        }, 1000);
        
        // Auto-scroll to show the result
        setTimeout(function() {
            $('#resultImage').addClass('fade-in');
        }, 500);
    });
    
    function createConfetti() {
        const confetti = $('<div>').css({
            position: 'fixed',
            top: '-10px',
            left: Math.random() * window.innerWidth + 'px',
            width: '10px',
            height: '10px',
            backgroundColor: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#6c5ce7'][Math.floor(Math.random() * 5)],
            borderRadius: '50%',
            zIndex: 1000,
            animation: 'fall 3s linear forwards'
        });
        
        $('body').append(confetti);
        
        setTimeout(function() {
            confetti.remove();
        }, 3000);
    }
    
    // Add CSS for confetti animation
    $('<style>').text(`
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `).appendTo('head');
</script>
@endsection

@php
$currentStep = 6;
@endphp
