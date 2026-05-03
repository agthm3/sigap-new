@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="bg-white p-5 rounded-xl shadow mb-4 flex justify-between items-center">
        <div>
            <h1 class="text-lg font-bold">Hasil Review</h1>
            <p class="text-sm text-gray-500">{{ $inovasi->judul }}</p>
        </div>

        <div class="text-right">
            <p class="text-xs text-gray-500">Total Poin</p>
            <p class="text-2xl font-bold text-maroon">{{ $totalPoint }}</p>
        </div>
    </div>

    <!-- REVIEWER -->
    <div class="bg-white p-4 rounded-xl shadow mb-4">
        <h3 class="font-semibold mb-2">Reviewer</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($reviewers as $items)
                @php $user = $items->first()->reviewer; @endphp
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">
                    👤 {{ $user->name }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- LIST REVIEW -->
    <div class="grid md:grid-cols-2 gap-4">

        @foreach($templates as $tpl)

        @php
            $fieldReviews = $grouped[$tpl->field] ?? collect();
        @endphp

        <div class="bg-white p-4 rounded-xl border">

            <!-- TITLE -->
            <div class="flex justify-between mb-2">
                <h3 class="font-semibold text-sm">{{ $tpl->label }}</h3>
                <span class="text-xs text-gray-400">{{ $tpl->point }} poin</span>
            </div>

            <!-- REVIEW PER USER -->
            <div class="space-y-2">

                @forelse($fieldReviews as $rev)

                @php
                    $color = match($rev->status) {
                        'accept' => 'green',
                        'revisi' => 'yellow',
                        'tolak'  => 'red',
                        default  => 'gray'
                    };
                @endphp

                <div class="border rounded p-2 bg-{{ $color }}-50">

                    <div class="flex justify-between items-center text-xs mb-1">
                        <span class="font-semibold">
                            {{ $rev->reviewer->name }}
                        </span>

                        <span class="px-2 py-0.5 rounded bg-{{ $color }}-100 text-{{ $color }}-700">
                            {{ strtoupper($rev->status) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-700">
                        {{ $rev->comment ?? '-' }}
                    </p>

                </div>

                @empty
                    <p class="text-sm text-gray-400 italic">
                        Belum direview
                    </p>
                @endforelse

            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection