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
    $webhook = new Webhook();
    $webhook->name = request('name');
    $webhook->url = request('url');
    $webhook->secret = request('secret');
    $webhook->save();
    return redirect('/webhooks');
}

}
