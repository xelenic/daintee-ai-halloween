<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halloween Dracula Kiosk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles for kiosk screen -->
    <style>
        body {
            width: 375.688px;
            height: 699px;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        
        .kiosk-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .halloween-bg {
            background: linear-gradient(135deg, #2d1b69 0%, #11998e 100%);
        }
        
        .spooky-text {
            color: #ff6b6b;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .glow-effect {
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ff6b6b;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .camera-container {
            width: 100%;
            height: 100%;
            position: relative;
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #ff6b6b;
            border: 4px solid white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .capture-button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(255, 107, 107, 0.8);
        }
        
        .step-indicator {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        
        .step-dot.active {
            background: #ff6b6b;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.8);
        }
        
        .step-dot.completed {
            background: #4ade80;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
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
        <div class="flex-1 flex flex-col items-center justify-center p-6 fade-in">
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
    </script>
    @yield('scripts')
</body>
</html>
