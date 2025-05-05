@props(['type' => 'card', 'count' => 1, 'class' => ''])

@php
    $types = [
        'card' => 'h-32 rounded-md',
        'text' => 'h-4 rounded',
        'image' => 'aspect-video rounded-md',
        'avatar' => 'h-12 w-12 rounded-full',
        'table-row' => 'h-16 rounded',
    ];
    
    $baseClass = $types[$type] ?? $types['card'];
@endphp

<div class="animate-pulse {{ $class }}">
    @if($type === 'table-row')
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
            <div class="w-20 h-6 bg-gray-200 rounded"></div>
        </div>
    @elseif($type === 'card-with-image')
        <div class="rounded-md overflow-hidden">
            <div class="aspect-video bg-gray-200"></div>
            <div class="p-4 space-y-3">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        </div>
    @else
        @for($i = 0; $i < $count; $i++)
            <div class="{{ $baseClass }} bg-gray-200 mb-2"></div>
        @endfor
    @endif
</div> 