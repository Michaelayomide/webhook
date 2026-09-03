<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookDispatch;
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
    $webhooks = Webhook::with('logs')->get();

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



public function testSend(Request $request, Webhook $webhook)
{
    // Validate inputs
    $request->validate([
        'event'       => 'required|string',
        'custom_data' => 'nullable|string',
    ]);

    // Parse JSON string into PHP array safely
    $customData = json_decode($request->input('custom_data'), true);

    // Fallback if user passes invalid or empty JSON
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($customData)) {
        $customData = ['message' => 'Default or invalid JSON payload'];
    }

    // Construct final payload structure
    $payload = [
        'event'     => $request->input('event'),
        'timestamp' => now()->toIso8601String(),
        'data'      => $customData,
    ];

    try {
        ProcessWebhookDispatch::dispatch($webhook, $payload);
    } catch (\Throwable $e) {
        // Suppress local sync exceptions so UI renders captured log
    }

    return redirect('/webhooks')->with('status', 'Custom webhook dispatched!');
}
}
