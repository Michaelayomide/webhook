<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Webhook</title>
</head>
<body>

    <h1>Edit Webhook #{{ $webhook->id }}</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/webhooks/{{ $webhook->id }}">
        @csrf
        @method('PUT')

        <div>
            <label>Name:</label><br>
            <input type="text" name="name" value="{{ old('name', $webhook->name) }}">
        </div>
        <br>

        <div>
            <label>Destination URL:</label><br>
            <input type="url" name="url" value="{{ old('url', $webhook->url) }}">
        </div>
        <br>

        <div>
            <label>Secret:</label><br>
            <input type="text" name="secret" value="{{ old('secret', $webhook->secret) }}">
        </div>
        <br>

        <button type="submit">Update Webhook</button>
    </form>

</body>
</html>