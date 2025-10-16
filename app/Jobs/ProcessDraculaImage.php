<?php

namespace App\Jobs;

use App\Models\KioskSession;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDraculaImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;

    /**
     * Create a new job instance.
     */
    public function __construct(KioskSession $session)
    {
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiService $geminiService): void
    {
        Log::info('Starting AI processing job for session: ' . $this->session->session_id);

        try {
            // Update status to processing
            $this->session->update(['status' => 'processing']);

            // Process the image using Gemini service
            $result = $geminiService->generateDraculaImage($this->session->original_image_path);

            if ($result['success']) {
                // Update session with processed image
                $this->session->update([
                    'processed_image_path' => $result['processed_image_path'],
                    'gemini_response' => $result,
                    'status' => 'completed',
                    'completed_at' => now()
                ]);

                Log::info('AI processing completed successfully for session: ' . $this->session->session_id);
            } else {
                // Update session with failure
                $this->session->update([
                    'status' => 'failed',
                    'gemini_response' => $result
                ]);

                Log::error('AI processing failed for session: ' . $this->session->session_id, $result);
            }
        } catch (\Exception $e) {
            // Update session with error
            $this->session->update([
                'status' => 'failed',
                'gemini_response' => ['error' => $e->getMessage()]
            ]);

            Log::error('AI processing job failed with exception for session: ' . $this->session->session_id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AI processing job failed for session: ' . $this->session->session_id, [
            'error' => $exception->getMessage()
        ]);

        // Update session status to failed
        $this->session->update([
            'status' => 'failed',
            'gemini_response' => ['error' => $exception->getMessage()]
        ]);
    }
}