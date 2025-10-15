@extends('kiosk.layout')

@section('content')
<div class="text-center text-white w-full">
    <div class="mb-8">
        <h1 class="text-3xl font-bold spooky-text mb-4">📱</h1>
        <h2 class="text-2xl font-bold spooky-text mb-2">ENTER YOUR PHONE</h2>
        <p class="text-gray-300">We'll send your Dracula photo here!</p>
    </div>
    
    <form id="phoneForm" class="w-full max-w-xs mx-auto">
        <div class="mb-6">
            <input 
                type="tel" 
                id="phone_number" 
                name="phone_number" 
                placeholder="Enter your phone number"
                class="w-full px-4 py-3 text-lg text-center bg-black bg-opacity-50 border-2 border-red-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-500"
                required
                autocomplete="tel"
            >
        </div>
        
        <div class="mb-6">
            <button 
                type="submit" 
                id="submitBtn"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg glow-effect transition-all duration-300 transform hover:scale-105"
            >
                CONTINUE
            </button>
        </div>
        
        <div class="mb-4">
            <button 
                type="button" 
                onclick="goBack()"
                class="text-gray-400 hover:text-white transition-colors duration-300"
            >
                ← Back to Welcome
            </button>
        </div>
    </form>
    
    <!-- Loading overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden">
        <div class="text-center text-white">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p>Processing...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function goBack() {
        window.location.href = "{{ route('kiosk.welcome') }}";
    }
    
    $('#phoneForm').on('submit', function(e) {
        e.preventDefault();
        
        const phoneNumber = $('#phone_number').val().trim();
        
        if (!phoneNumber) {
            alert('Please enter your phone number');
            return;
        }
        
        // Show loading
        $('#loadingOverlay').removeClass('hidden');
        $('#submitBtn').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('kiosk.phone.process') }}",
            method: 'POST',
            data: {
                phone_number: phoneNumber
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error: ' + (response.message || 'Please try again'));
                    $('#loadingOverlay').addClass('hidden');
                    $('#submitBtn').prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.phone_number) {
                        errorMessage = errors.phone_number[0];
                    }
                }
                
                alert(errorMessage);
                $('#loadingOverlay').addClass('hidden');
                $('#submitBtn').prop('disabled', false);
            }
        });
    });
    
    // Format phone number as user types
    $('#phone_number').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            }
        }
        this.value = value;
    });
</script>
@endsection

@php
$currentStep = 2;
@endphp
