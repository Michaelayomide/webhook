<h1>my webhooks</h1>
@foreach ( $webhooks as $webhook)
 <p>{{ $webhook->name }}</p>
 <p>{{ $webhook->url }}</p>
 {{-- <p>{{ $webhook->secret }}</p> --}}
  @if ($webhook->status)
  <p>Active</p>
    
  @else
    <P>Inactive</P>
  @endif
@endforeach