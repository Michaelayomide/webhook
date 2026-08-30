<?php

namespace App\Http\Controllers;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
//     public function index()
// {
//     return 'Webhook page is working!';
// }
 public function index()
{
  $webhooks = Webhook::all();
  return view('webhooks.index', compact('webhooks'));
}

public function create()
{
    return view('webhooks.create');
}

public function store()
    {
        $validated = request()->validate([
            'name'   => 'required|string|max:255',
            'url'    => 'required|url',
            'secret' => 'required|string|min:16',
        ]);

        Webhook::create($validated);

        return redirect('/webhooks');
    }


public function edit(Webhook $webhook)
{
   // return "Controller is reaching here! Webhook ID: " . $webhook->id;
   return view('webhooks.edit', compact('webhook'));
}

public function update(Webhook $webhook)
{
    $validated = request()->validate([
        'name' => 'required|string|max:255',
        'url' => 'required|url',
        'secret' => 'required|string|max:16'
    ]);
    $webhook->update($validated);
    return redirect('/webhooks');

}

//delete a webhook
public function destroy(Webhook $webhook)
{
    $webhook->delete();

    return redirect('/webhooks');

}

public function testSend(Webhook $webhook)
{
    $startTime = microtime(true);
    $payload = [
        'event' =>'user.updated',
        'timestamp'=> now()->toIso8601String(),
        'date'=> [
            'id'=> 101,
            'name'=> 'Test User',

        ],
        

    ];
    try{
        $response = Http::timeout(5)->post($webhook->url, $payload);
        $responseTimeMs = (microtime(true)-$startTime) * 1000;
    
        WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => 'user.updated',
            'payload'=> json_encode($payload),
            'response_time_ms'=> round($responseTimeMs, 2),
            'is_successful'=> $response->successful(),
            'error_message'=> null,

        ]);


    }catch(\Exception $e){
        $responseTimeMs = (microtime(true)-$startTime) * 1000;
        WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => 'user.updated',
            'payload'=> json_encode($payload),
            'status_code'=> null,
            'response_time_ms'=> round($responseTimeMs, 2),
            'is_successful'=> false,
            'error_message'=> $e->getMessage(),

        ]);
    }
    return redirect('/webhooks');

}

