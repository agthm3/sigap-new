@if($status)
<span class="ml-2 px-2 py-0.5 text-xs rounded
  @if($status=='accept') bg-green-100 text-green-700
  @elseif($status=='revisi') bg-yellow-100 text-yellow-700
  @elseif($status=='tolak') bg-red-100 text-red-700
  @endif">
  {{ strtoupper($status) }}
</span>
@endif