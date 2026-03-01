<div class="p-3 border rounded-lg bg-gray-50 {{ $class ?? '' }}">
    <p class="text-xs text-gray-500">{{ $label }}</p>
    <p class="font-semibold text-gray-900">
        {{ $value ?: '—' }}
    </p>
</div>