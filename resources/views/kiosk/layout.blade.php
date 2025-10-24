<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Halloween Dracula Kiosk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom styles for kiosk screen -->
    <style>
        body {
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Prevent zoom and ensure stable layout */
            zoom: 1;
            transform: scale(1);
            transform-origin: 0 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        .kiosk-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            background: #000;
            overflow: hidden;
            /* Prevent zoom and ensure stable layout */
            zoom: 1;
            transform: scale(1);
            transform-origin: 0 0;
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            -o-transform: scale(1);
        }

        /* Prevent zoom on all elements */
        * {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Allow text selection only for input fields */
        input, textarea {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }


        /* Responsive typography using vh */
        .responsive-text {
            font-size: 2.5vh;
        }

        .responsive-title {
            font-size: 4vh;
        }

        .responsive-subtitle {
            font-size: 2vh;
        }

        /* Responsive buttons using vh */
        .responsive-btn {
            padding: 1.5vh 3vh;
            font-size: 2.5vh;
        }

        /* Responsive images */
        .responsive-img {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        /* Ensure content fits within container */
        .content-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2vh;
            box-sizing: border-box;
        }

        /* Touch-friendly interactions for kiosk */
        button, .clickable {
            min-height: 6vh;
            min-width: 6vh;
            touch-action: manipulation;
        }

        /* Prevent zoom on double tap */
        * {
            touch-action: manipulation;
        }

        /* Kiosk-specific optimizations */
        @media (orientation: landscape) {
            .kiosk-container {
                max-width: 100vh;
                max-height: 100vw;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .responsive-img {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }

        .halloween-bg {
            background: linear-gradient(135deg, #2d1b69 0%, #11998e 100%);
        }

        .spooky-text {
            color: #ff6b6b;
            text-shadow: 0.3vh 0.3vh 0.6vh rgba(0,0,0,0.5);
        }

        .glow-effect {
            box-shadow: 0 0 2.5vh rgba(255, 107, 107, 0.5);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(2.5vh); }
            to { opacity: 1; transform: translateY(0); }
        }

        .loading-spinner {
            border: 0.5vh solid #f3f3f3;
            border-top: 0.5vh solid #ff6b6b;
            border-radius: 50%;
            width: 6vh;
            height: 6vh;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .camera-container {
            position: relative;
            display: inline-block;
        }

        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .capture-button {
            width: 10vh;
            height: 10vh;
            border-radius: 50%;
            background: #ff6b6b;
            border: 0.5vh solid white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3vh;
            color: white;
            transition: all 0.3s ease;
        }

        .capture-button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 3.5vh rgba(255, 107, 107, 0.8);
        }

        .step-indicator {
            position: absolute;
            top: 2.5vh;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1.25vh;
            z-index: 10;
        }

        .step-dot {
            width: 1.5vh;
            height: 1.5vh;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #ff6b6b;
            box-shadow: 0 0 1.25vh rgba(255, 107, 107, 0.8);
        }

        .step-dot.completed {
            background: #4ade80;
        }

        /* Horror Animation Styles */
        .horror-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
            overflow: hidden;
        }

        /* Lightning Effects */
        .lightning-flash {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            opacity: 0;
            animation: lightning-flash 0.1s ease-out;
        }

        .lightning-bolt {
            position: absolute;
            width: 0.375vh;
            height: 100%;
            background: linear-gradient(to bottom,
                transparent 0%,
                rgba(255, 255, 255, 0.8) 20%,
                rgba(173, 216, 230, 0.9) 50%,
                rgba(255, 255, 255, 0.8) 80%,
                transparent 100%);
            opacity: 0;
            filter: contrast(1.5);
            animation: lightning-bolt 0.3s ease-out;
        }

        /* Floating Particles */
        .floating-particle {
            position: absolute;
            width: 0.25vh;
            height: 0.25vh;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: float-particle 8s infinite linear;
        }

        .floating-particle:nth-child(odd) {
            background: rgba(200, 200, 200, 0.4);
            animation-duration: 12s;
        }

        .floating-particle:nth-child(3n) {
            background: rgba(150, 150, 150, 0.3);
            animation-duration: 15s;
        }

        /* Glitch Effect */
        .glitch-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 0, 0, 0.1);
            opacity: 0;
            animation: glitch-effect 0.3s ease-out;
        }

        /* Flickering Effect */
        .flicker-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: flicker 0.1s ease-out;
        }

        /* Spooky Shadows */
        .spooky-shadow {
            position: absolute;
            width: 2.5vh;
            height: 2.5vh;
            background: radial-gradient(circle, rgba(0, 0, 0, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: spooky-float 6s infinite ease-in-out;
        }

        /* Keyframe Animations */
        @keyframes lightning-flash {
            0% { opacity: 0; }
            10% { opacity: 0.9; }
            20% { opacity: 0.1; }
            30% { opacity: 0.8; }
            40% { opacity: 0.2; }
            50% { opacity: 0.7; }
            60% { opacity: 0.1; }
            70% { opacity: 0.6; }
            80% { opacity: 0.1; }
            90% { opacity: 0.4; }
            100% { opacity: 0; }
        }

        @keyframes lightning-bolt {
            0% {
                opacity: 0;
                transform: translateX(-6.25vh) scaleY(0.1);
            }
            20% {
                opacity: 1;
                transform: translateX(0) scaleY(1);
            }
            80% {
                opacity: 1;
                transform: translateX(0) scaleY(1);
            }
            100% {
                opacity: 0;
                transform: translateX(6.25vh) scaleY(0.1);
            }
        }

        @keyframes float-particle {
            0% {
                transform: translateY(100vh) translateX(0vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-12.5vh) translateX(12.5vh) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes glitch-effect {
            0% { opacity: 0; transform: translateX(0); }
            20% { opacity: 0.1; transform: translateX(-0.25vh); }
            40% { opacity: 0.2; transform: translateX(0.25vh); }
            60% { opacity: 0.1; transform: translateX(-0.125vh); }
            80% { opacity: 0.05; transform: translateX(0.125vh); }
            100% { opacity: 0; transform: translateX(0); }
        }

        @keyframes flicker {
            0% { opacity: 0; }
            50% { opacity: 0.1; }
            100% { opacity: 0; }
        }

        @keyframes spooky-float {
            0%, 100% {
                transform: translateY(0vh) translateX(0vh) scale(1);
                opacity: 0.3;
            }
            25% {
                transform: translateY(-2.5vh) translateX(1.25vh) scale(1.2);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-1.25vh) translateX(-1.875vh) scale(0.8);
                opacity: 0.4;
            }
            75% {
                transform: translateY(-3.75vh) translateX(0.625vh) scale(1.1);
                opacity: 0.6;
            }
        }

        /* Screen Flicker */
        .screen-flicker {
            animation: screen-flicker 0.1s ease-out;
        }
        
        @keyframes screen-flicker {
            0% { filter: brightness(1); }
            50% { filter: brightness(0.8); }
            100% { filter: brightness(1); }
        }
        
        /* Full Screen Button */
        .fullscreen-btn {
            position: absolute;
            top: 2.5vh;
            right: 2.5vh;
            width: 5vh;
            height: 5vh;
            background: rgba(0, 0, 0, 0.7);
            border: 0.25vh solid rgba(255, 255, 255, 0.3);
            border-radius: 1vh;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25vh;
            transition: all 0.3s ease;
            z-index: 2000;
            backdrop-filter: blur(1.25vh);
        }
        
        .fullscreen-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.1);
            box-shadow: 0 0 1.875vh rgba(255, 255, 255, 0.3);
        }
        
        .fullscreen-btn:active {
            transform: scale(0.95);
        }
        
        .fullscreen-btn svg {
            width: 2.5vh;
            height: 2.5vh;
            fill: currentColor;
        }

        /* Home Button */
        .home-btn {
            position: absolute;
            top: 2.5vh;
            left: 2.5vh;
            width: 5vh;
            height: 5vh;
            background: rgba(0, 0, 0, 0.7);
            border: 0.25vh solid rgba(255, 255, 255, 0.3);
            border-radius: 1vh;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25vh;
            transition: all 0.3s ease;
            z-index: 2000;
            backdrop-filter: blur(1.25vh);
        }
        
        .home-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.1);
            box-shadow: 0 0 1.875vh rgba(255, 255, 255, 0.3);
        }
        
        .home-btn:active {
            transform: scale(0.95);
        }
        
        .home-btn svg {
            width: 2.5vh;
            height: 2.5vh;
            fill: currentColor;
        }

        /* Countdown Animation */
        #countdownNumber {
            transition: all 0.3s ease;
            animation: countdownPulse 1s ease-in-out infinite;
        }

        @keyframes countdownPulse {
            0% {
                transform: scale(1);
                text-shadow: 0 0 2vh rgba(255, 0, 0, 0.8);
            }
            50% {
                transform: scale(1.1);
                text-shadow: 0 0 4vh rgba(255, 0, 0, 1);
            }
            100% {
                transform: scale(1);
                text-shadow: 0 0 2vh rgba(255, 0, 0, 0.8);
            }
        }

        /* Hidden utility class */
        .hidden {
            display: none !important;
        }
        
        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: opacity 0.5s ease-out;
        }
        
        .preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .preloader-content {
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .preloader-logo {
            width: 25vh;
            height: auto;
            margin: 0 auto 2.5vh auto;
            animation: preloader-pulse 2s infinite;
            filter: drop-shadow(0 0 2.5vh rgba(255, 107, 107, 0.5));
            display: block;
        }
        
        .preloader-spinner {
            width: 7.5vh;
            height: 7.5vh;
            border: 0.5vh solid rgba(255, 107, 107, 0.3);
            border-top: 0.5vh solid #ff6b6b;
            border-radius: 50%;
            animation: preloader-spin 1s linear infinite;
            margin: 0 auto 2.5vh;
        }
        
        .preloader-text {
            font-size: 1.5vh;
            margin-bottom: 1.25vh;
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .preloader-progress {
            width: 25vh;
            height: 0.5vh;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 0.25vh;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .preloader-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 0.25vh;
        }
        
        .preloader-percentage {
            font-size: 1.125vh;
            color: #ccc;
            margin-top: 0.625vh;
        }
        
        .preloader-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .preloader-particle {
            position: absolute;
            width: 0.25vh;
            height: 0.25vh;
            background: rgba(255, 107, 107, 0.6);
            border-radius: 50%;
            animation: preloader-float 4s infinite linear;
        }
        
        @keyframes preloader-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        
        @keyframes preloader-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes preloader-float {
            0% {
                transform: translateY(100vh) translateX(0vh);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-12.5vh) translateX(12.5vh);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-particles" id="preloaderParticles"></div>
        <div class="preloader-content">
            <img src="{{ asset('01/Dracula Logo.png') }}" alt="Dracula Logo" class="preloader-logo">
            <div class="preloader-spinner"></div>
            <div class="preloader-text" id="preloaderText">Loading your spooky experience...</div>
            <div class="preloader-progress">
                <div class="preloader-progress-bar" id="preloaderProgress"></div>
            </div>
            <div class="preloader-percentage" id="preloaderPercentage">0%</div>
        </div>
    </div>
    
    <div class="kiosk-container">
        <!-- Home Button -->
        <button class="home-btn" id="homeBtn" onclick="goHome()" title="Go to Home">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </button>

        <!-- Full Screen Button -->
        <button class="fullscreen-btn" id="fullscreenBtn" onclick="toggleFullscreen()" title="Toggle Full Screen">
            <svg id="fullscreenIcon" viewBox="0 0 24 24">
                <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
            </svg>
        </button>
        
        <!-- Horror Animation Overlay -->
        <div class="horror-overlay" id="horrorOverlay">
            <!-- Particles and effects will be generated here -->
        </div>
        
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-dot {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}"></div>
            <div class="step-dot {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}"></div>
            <div class="step-dot {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}"></div>
            <div class="step-dot {{ $currentStep >= 4 ? 'active' : '' }} {{ $currentStep > 4 ? 'completed' : '' }}"></div>
            <div class="step-dot {{ $currentStep >= 5 ? 'active' : '' }} {{ $currentStep > 5 ? 'completed' : '' }}"></div>
            <div class="step-dot {{ $currentStep >= 6 ? 'active' : '' }} {{ $currentStep > 6 ? 'completed' : '' }}"></div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center fade-in">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // CSRF token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Horror Animation System
        class HorrorAnimations {
            constructor() {
                this.overlay = document.getElementById('horrorOverlay');
                this.particles = [];
                this.shadows = [];
                this.init();
            }

            init() {
                this.createFloatingParticles();
                this.createSpookyShadows();
                this.startLightningStorm();
                this.startGlitchEffects();
                this.startScreenFlicker();
            }

            // Create floating dust particles
            createFloatingParticles() {
                for (let i = 0; i < 15; i++) {
                    setTimeout(() => {
                        this.createParticle();
                    }, i * 200);
                }

                // Continuously create new particles
                setInterval(() => {
                    if (this.particles.length < 20) {
                        this.createParticle();
                    }
                }, 3000);
            }

            createParticle() {
                const particle = document.createElement('div');
                particle.className = 'floating-particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 5 + 's';
                particle.style.animationDuration = (8 + Math.random() * 7) + 's';

                this.overlay.appendChild(particle);
                this.particles.push(particle);

                // Remove particle after animation
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                        this.particles = this.particles.filter(p => p !== particle);
                    }
                }, 15000);
            }

            // Create floating spooky shadows
            createSpookyShadows() {
                for (let i = 0; i < 8; i++) {
                    setTimeout(() => {
                        this.createShadow();
                    }, i * 1000);
                }

                setInterval(() => {
                    if (this.shadows.length < 10) {
                        this.createShadow();
                    }
                }, 8000);
            }

            createShadow() {
                const shadow = document.createElement('div');
                shadow.className = 'spooky-shadow';
                shadow.style.left = Math.random() * 100 + '%';
                shadow.style.top = Math.random() * 100 + '%';
                shadow.style.animationDelay = Math.random() * 3 + 's';
                shadow.style.animationDuration = (4 + Math.random() * 4) + 's';

                this.overlay.appendChild(shadow);
                this.shadows.push(shadow);

                setTimeout(() => {
                    if (shadow.parentNode) {
                        shadow.parentNode.removeChild(shadow);
                        this.shadows = this.shadows.filter(s => s !== shadow);
                    }
                }, 10000);
            }

            // Lightning storm system
            startLightningStorm() {
                // Initial lightning after 3 seconds
                setTimeout(() => {
                    this.createLightning();
                }, 3000);

                // Random lightning strikes
                setInterval(() => {
                    if (Math.random() < 0.25) { // 25% chance
                        this.createLightning();
                    }
                }, 4000 + Math.random() * 6000); // Every 4-10 seconds
            }

            createLightning() {
                // Create lightning flash
                const flash = document.createElement('div');
                flash.className = 'lightning-flash';
                this.overlay.appendChild(flash);

                // Create lightning bolt
                const bolt = document.createElement('div');
                bolt.className = 'lightning-bolt';
                bolt.style.left = Math.random() * 100 + '%';
                bolt.style.transform = `rotate(${(Math.random() - 0.5) * 15}deg)`;
                this.overlay.appendChild(bolt);

                // Remove elements after animation
                setTimeout(() => {
                    if (flash.parentNode) flash.parentNode.removeChild(flash);
                    if (bolt.parentNode) bolt.parentNode.removeChild(bolt);
                }, 300);
            }

            // Glitch effects
            startGlitchEffects() {
                setInterval(() => {
                    if (Math.random() < 0.15) { // 15% chance
                        this.createGlitch();
                    }
                }, 2000 + Math.random() * 5000); // Every 2-7 seconds
            }

            createGlitch() {
                const glitch = document.createElement('div');
                glitch.className = 'glitch-overlay';
                this.overlay.appendChild(glitch);

                setTimeout(() => {
                    if (glitch.parentNode) {
                        glitch.parentNode.removeChild(glitch);
                    }
                }, 300);
            }

            // Screen flicker effects
            startScreenFlicker() {
                setInterval(() => {
                    if (Math.random() < 0.1) { // 10% chance
                        this.createFlicker();
                    }
                }, 1000 + Math.random() * 3000); // Every 1-4 seconds
            }

            createFlicker() {
                const flicker = document.createElement('div');
                flicker.className = 'flicker-overlay';
                this.overlay.appendChild(flicker);

                // Also add screen flicker to the main container
                document.querySelector('.kiosk-container').classList.add('screen-flicker');

                setTimeout(() => {
                    if (flicker.parentNode) {
                        flicker.parentNode.removeChild(flicker);
                    }
                    document.querySelector('.kiosk-container').classList.remove('screen-flicker');
                }, 100);
            }
        }

        // Full Screen Functionality
        function toggleFullscreen() {
            const element = document.documentElement;
            const fullscreenIcon = document.getElementById('fullscreenIcon');
            
            if (!document.fullscreenElement) {
                // Enter fullscreen
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
                
                // Update icon to exit fullscreen
                fullscreenIcon.innerHTML = '<path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>';
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                
                // Update icon to enter fullscreen
                fullscreenIcon.innerHTML = '<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>';
            }
        }

        // Home Button Functionality
        function goHome() {
            window.location.href = "{{ route('kiosk.welcome') }}";
        }
        
        // Listen for fullscreen changes to update icon
        document.addEventListener('fullscreenchange', updateFullscreenIcon);
        document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
        document.addEventListener('msfullscreenchange', updateFullscreenIcon);
        
        function updateFullscreenIcon() {
            const fullscreenIcon = document.getElementById('fullscreenIcon');
            const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
            
            if (isFullscreen) {
                fullscreenIcon.innerHTML = '<path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>';
            } else {
                fullscreenIcon.innerHTML = '<path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>';
            }
        }
        
        // Preloader System
        class PreloaderSystem {
            constructor() {
                this.preloader = document.getElementById('preloader');
                this.progressBar = document.getElementById('preloaderProgress');
                this.percentage = document.getElementById('preloaderPercentage');
                this.text = document.getElementById('preloaderText');
                this.particlesContainer = document.getElementById('preloaderParticles');
                
                this.loadedImages = 0;
                this.totalImages = 0;
                this.images = [];
                this.loadingMessages = [
                    'Loading your spooky experience...',
                    'Preparing the transformation...',
                    'Gathering dark magic...',
                    'Summoning Count Dracula...',
                    'Almost ready...'
                ];
                
                this.init();
            }
            
            init() {
                this.createParticles();
                this.findAllImages();
                this.startLoading();
            }
            
            createParticles() {
                for (let i = 0; i < 20; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'preloader-particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 4 + 's';
                    particle.style.animationDuration = (3 + Math.random() * 2) + 's';
                    this.particlesContainer.appendChild(particle);
                }
            }
            
            findAllImages() {
                // Find all images in the page
                const allImages = document.querySelectorAll('img');
                const backgroundImages = this.findBackgroundImages();
                
                this.images = [...allImages, ...backgroundImages];
                this.totalImages = this.images.length;
                
            }
            
            findBackgroundImages() {
                const elements = [];
                const allElements = document.querySelectorAll('*');
                
                allElements.forEach(el => {
                    const bgImage = window.getComputedStyle(el).backgroundImage;
                    if (bgImage && bgImage !== 'none' && bgImage.includes('url(')) {
                        const urlMatch = bgImage.match(/url\(['"]?([^'"]+)['"]?\)/);
                        if (urlMatch) {
                            const img = new Image();
                            img.src = urlMatch[1];
                            elements.push(img);
                        }
                    }
                });
                
                return elements;
            }
            
            startLoading() {
                if (this.totalImages === 0) {
                    this.complete();
                    return;
                }
                
                this.images.forEach((img, index) => {
                    if (img.complete) {
                        this.onImageLoad();
                    } else {
                        img.onload = () => this.onImageLoad();
                        img.onerror = () => this.onImageLoad();
                    }
                });
                
                // Update loading messages
                this.updateLoadingMessage();
            }
            
            onImageLoad() {
                this.loadedImages++;
                const progress = (this.loadedImages / this.totalImages) * 100;
                this.updateProgress(progress);
                
                if (this.loadedImages >= this.totalImages) {
                    setTimeout(() => this.complete(), 500);
                }
            }
            
            updateProgress(progress) {
                this.progressBar.style.width = progress + '%';
                this.percentage.textContent = Math.round(progress) + '%';
            }
            
            updateLoadingMessage() {
                let messageIndex = 0;
                const messageInterval = setInterval(() => {
                    if (messageIndex < this.loadingMessages.length - 1) {
                        this.text.textContent = this.loadingMessages[messageIndex];
                        messageIndex++;
                    } else {
                        clearInterval(messageInterval);
                    }
                }, 1000);
            }
            
            complete() {
                this.text.textContent = 'Welcome to the darkness...';
                this.updateProgress(100);
                
                setTimeout(() => {
                    this.preloader.classList.add('hidden');
                    setTimeout(() => {
                        this.preloader.style.display = 'none';
                        // Initialize horror animations after preloader
                        new HorrorAnimations();
                    }, 500);
                }, 1000);
            }
        }
        
        // Initialize preloader when page loads
        $(document).ready(function() {
            new PreloaderSystem();
        });
    </script>
    @yield('scripts')
</body>
</html>
