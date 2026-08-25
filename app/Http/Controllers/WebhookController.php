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

public function create()
{
    return view('webhooks.create');
}
}
