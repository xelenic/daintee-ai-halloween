<?php

namespace App\Http\Controllers;

use App\Models\KioskSession;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KioskController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show the welcome screen (Step 1)
     */
    public function welcome()
    {
        return view('kiosk.welcome');
    }

    /**
     * Show the phone number input screen (Step 2)
     */
    public function phoneInput()
    {
        return view('kiosk.phone-input');
    }

    /**
     * Process phone number and create session (Step 2 -> Step 3)
     */
    public function processPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new kiosk session
        $session = KioskSession::create([
            'phone_number' => $request->phone_number,
            'status' => 'phone_collected'
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->session_id,
            'redirect' => route('kiosk.camera', $session->session_id)
        ]);
    }

    /**
     * Show the camera capture screen (Step 3)
     */
    public function camera($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        if ($session->status !== 'phone_collected') {
            return redirect()->route('kiosk.welcome');
        }

        return view('kiosk.camera', compact('session'));
    }

    /**
     * Process captured photo (Step 3 -> Step 4)
     */
    public function processPhoto(Request $request, $sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Store the original image
        $imagePath = $request->file('photo')->store('kiosk/original', 'public');

        $session->update([
            'original_image_path' => $imagePath,
            'status' => 'photo_captured'
        ]);

        return response()->json([
            'success' => true,
            'image_url' => Storage::url($imagePath),
            'redirect' => route('kiosk.preview', $session->session_id)
        ]);
    }

    /**
     * Show the photo preview screen (Step 4)
     */
    public function preview($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        if ($session->status !== 'photo_captured') {
            return redirect()->route('kiosk.welcome');
        }

        return view('kiosk.preview', compact('session'));
    }

    /**
     * Confirm photo and start processing (Step 4 -> Step 5)
     */
    public function confirmPhoto($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        $session->update(['status' => 'photo_confirmed']);

        return response()->json([
            'success' => true,
            'redirect' => route('kiosk.processing', $session->session_id)
        ]);
    }

    /**
     * Show the processing screen (Step 5)
     */
    public function processing($sessionId)
    {
        \Log::info('Processing screen accessed for session: ' . $sessionId);

        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        \Log::info('Session status: ' . $session->status);

        if ($session->status !== 'photo_confirmed') {
            \Log::info('Session status not photo_confirmed, redirecting to welcome');
            return redirect()->route('kiosk.welcome');
        }

        // Start processing in the background
        \Log::info('Starting image processing...');
        $this->processImage($session);

        return view('kiosk.processing', compact('session'));
    }

    /**
     * Check processing status (AJAX endpoint)
     */
    public function checkStatus($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        return response()->json([
            'status' => $session->status,
            'completed' => $session->isCompleted(),
            'redirect' => $session->isCompleted() ? route('kiosk.result', $session->session_id) : null
        ]);
    }

    /**
     * Show the final result screen (Step 6)
     */
    public function result($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        if (!$session->isCompleted()) {
            return redirect()->route('kiosk.processing', $session->session_id);
        }

        return view('kiosk.result', compact('session'));
    }

    /**
     * Process the image using Gemini API
     */
    private function processImage(KioskSession $session)
    {
        \Log::info('Processing image for session: ' . $session->session_id);
        \Log::info('Original image path: ' . $session->original_image_path);

        $session->update(['status' => 'processing']);

            // Use Gemini service to process the image
            \Log::info('Calling Gemini service...');
            $result = $this->geminiService->generateDraculaImage($session->original_image_path);
            \Log::info('Gemini service result: ', $result);

            if ($result['success']) {
                $session->update([
                    'processed_image_path' => $result['processed_image_path'],
                    'gemini_response' => $result,
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
            } else {
                $session->update([
                    'status' => 'failed',
                    'gemini_response' => $result
                ]);
            }

    }

    /**
     * Retake photo (go back to camera)
     */
    public function retakePhoto($sessionId)
    {
        $session = KioskSession::where('session_id', $sessionId)->firstOrFail();

        // Delete the previous image
        if ($session->original_image_path) {
            Storage::disk('public')->delete($session->original_image_path);
        }

        $session->update([
            'original_image_path' => null,
            'status' => 'phone_collected'
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('kiosk.camera', $session->session_id)
        ]);
    }

    /**
     * Start a new session
     */
    public function newSession()
    {
        return redirect()->route('kiosk.welcome');
    }
}
