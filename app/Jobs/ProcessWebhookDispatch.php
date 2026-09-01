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
     * number of times the job may be attempted
     */
    public $tries = 3;

    public$backoff =[10,30];
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
        $startTime = microtime(true);
        $jsonPayload = json_encode($this->payload);
        //Generate HMAC SHA-256 signature
        $signature = hash_hmac('sha256',$jsonPayload,$this->webhook->secret);

        $statusCode = null;
        $isSuccessful = false;
        $errorMessage = null;

        try{
            $response = Http::timeout(5)
            ->withHeaders([
                'X-Signature'=> $signature,
                'Content-Type' => 'application/json'
            ])
            ->withBody($jsonPayload,'application/json')
            ->post($this->webhook->url);
            $statusCode = $response->status();
            $isSuccessful = $response->successful();

            //fail explicaitly so laravel queu handle retry logic on bad staus codes
           if(!$isSuccessful){
            $errorMessage = "server responded with HTTP code : {$statusCode}";
           }


        }catch(\Throwable $e){
            $errorMessage = $e->getMessage();
        }
        $responseTimeMs  = (microtime(true)-$startTime)*1000;
        //persist excution log entry
        WebhookLog::create([
            'webhook_id' => $this->webhook->id,
            'event' => $this->payload['event'] ?? 'user.updated',
            'payload'=> $jsonPayload,
            'status_code' => $statusCode,
            'response_time_ms' => round($responseTimeMs,2),
            'is_successsful' => $isSuccessful,
            'error_message' => $errorMessage,
        ]);

        //Throw an exception if failed so  laravel increments attempts count and triggers
        if (!$isSuccessful){
            throw new \Exception("Webhook dispatch failed for ID{$this->webhook->id}:{$errorMessage}"); 
        }

    }   
}
