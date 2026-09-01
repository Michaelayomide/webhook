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

public function testSend(Webhook $webhook)
{
    $payload = [
        'event'     => 'user.updated',
        'timestamp' => now()->toIso8601String(),
        'data'      => [
            'id'   => 101,
            'name' => 'Test User',
        ],
    ];

    // Dispatch job to queue worker
    ProcessWebhookDispatch::dispatch($webhook, $payload);

    return redirect('/webhooks')->with('status', 'Webhook dispatch queued!');
}
}


