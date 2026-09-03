

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Webhooks</title>
</head>
<body>

    <h1>My Webhooks</h1>
    <a href="/webhooks/create">Create New Webhook</a>
    <hr>

    @foreach($webhooks as $webhook)
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
            <h3>#{{ $webhook->id }} - {{ $webhook->name }}</h3>
            <p><strong>URL:</strong> {{ $webhook->url }}</p>
            <p><strong>Status:</strong> {{ $webhook->status ? 'Active' : 'Inactive' }}</p>

            <div>
                <!-- Edit Link -->
                <a href="/webhooks/{{ $webhook->id }}/edit">Edit</a> | 

                <!-- Delete Form -->
                <form method="POST" action="/webhooks/{{ $webhook->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this webhook?')">Delete</button>
                </form> |

                <!--  Dynamic Test Dispatch Form -->

                <form method="POST" action="/webhooks/{{ $webhook->id }}/test" style="margin-top: 10px ;">
                  @csrf
                  <div style="margin-bottom :8px ;">
                    <label><strong>Event Types</strong></label>
                    <select name="event" style="padding: 4px;width: 100; max-width:300px;">
                        <option value="user.updated">user.updated</option>
                        <option value="order.created">order.created</option>
                        <option value="payment.successful">payment.successful</option>
                        <option value="invoice.payment_failed">invoice.payment_failed</option>
                    </select>
                 </div>  
                 <div style="margin-bottom: 8px;">
                    <label><strong>custom </strong></label><br>
                    <textarea name="custom_data" rows="4" style="width: 100%; max-width: 400px; font-family: monospace;" placeholder='{"id": 101, "amount": 2500, "currency": "USD"}'>{"id": 101, "status": "active"}</textarea>
                 </div>
                 <button type="submit">Send Custom Dispatch</button>
               </form>
               
            <h4>Delivery Logs</h4>
            @if($webhook->logs->isEmpty())
                <p><em>No dispatches logged yet. Click "Test Dispatch" to trigger one.</em></p>
            @else
                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Status Code</th>
                            <th>Response Time</th>
                            <th>Result</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($webhook->logs as $log)
                            <tr>
                                <td>{{ $log->event }}</td>
                                <td>{{ $log->status_code ?? 'N/A' }}</td>
                                <td>{{ $log->response_time_ms }} ms</td>
                                <td>
                                    <strong style="color: {{ $log->is_successful ? 'green' : 'red' }};">
                                        {{ $log->is_successful ? 'SUCCESS' : 'FAILED' }}
                                    </strong>
                                </td>
                                <td>{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

</body>
</html>