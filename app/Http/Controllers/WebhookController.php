<?php

namespace App\Http\Controllers;
use App\Models\Webhook;
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
}