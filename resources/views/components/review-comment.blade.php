@if($comments && count($comments))
  @foreach($comments as $c)
    <div class="mt-1 text-xs bg-gray-50 border rounded p-2 text-gray-600">
      💬 {{ $c }}
    </div>
  @endforeach
@endif