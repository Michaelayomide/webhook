

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

                <!-- Test Dispatch Form -->
                <form method="POST" action="/webhooks/{{ $webhook->id }}/test" style="display:inline;">
                    @csrf
                    <button type="submit">Test Dispatch</button>
                </form>
            </div>

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