<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessWebhookDispatch implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Exponential backoff delays in seconds.
     */
    public $backoff = [10, 30];

    protected $webhook;
    protected $payload;

    public function __construct(Webhook $webhook, array $payload)
    {
        $this->webhook = $webhook;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime   = microtime(true);
        $jsonPayload = json_encode($this->payload);

        $signature = hash_hmac('sha256', $jsonPayload, $this->webhook->secret);

        $statusCode   = null;
        $isSuccessful = false;
        $errorMessage = null;

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-Signature'  => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->withBody($jsonPayload, 'application/json')
                ->post($this->webhook->url);

            $statusCode   = $response->status();
            
            // Native Laravel check: returns TRUE for 200-299 HTTP codes
            $isSuccessful = $response->successful();

            if (!$isSuccessful) {
                $errorMessage = "Server responded with HTTP code: {$statusCode}";
            }
        } catch (\Throwable $e) {
            $isSuccessful = false;
            $errorMessage = $e->getMessage();
        }

        $responseTimeMs = (microtime(true) - $startTime) * 1000;

        // Save accurate state to database
        WebhookLog::create([
            'webhook_id'       => $this->webhook->id,
            'event'            => $this->payload['event'] ?? 'user.updated',
            'payload'          => $jsonPayload,
            'status_code'      => $statusCode,
            'response_time_ms' => round($responseTimeMs, 2),
            'is_successful'    => $isSuccessful,
            'error_message'    => $errorMessage,
        ]);

        if (!$isSuccessful) {
            throw new \Exception("Webhook dispatch failed for ID {$this->webhook->id}: {$errorMessage}");
        }
    }
}