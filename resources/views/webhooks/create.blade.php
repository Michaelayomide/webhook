<h1>Create Webhook</h1>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/webhooks">
    @csrf

    <div>
        <label>Webhook Name</label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>

    <div>
        <label>Destination URL</label>
        <input type="url" name="url" value="{{ old('url') }}">
    </div>

    <div>
        <label>Secret</label>
        <input type="password" name="secret">
    </div>

    <button type="submit">Create Webhook</button>
</form>