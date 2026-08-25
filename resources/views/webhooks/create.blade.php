<h1>Create Webhook</h1>
<form method="post" action="/webhooks">
    @csrf
    <label >webhook</label>
    <input type="text" name="name">

    <label>Destination ULR</label>
    <input type="url" name="url">

    <label>Secret</label>
    <input type="password" name="secret">

    <button type="submit">Create webhook</button>
</form>
