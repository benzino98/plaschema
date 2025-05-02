@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">Frequently Asked Questions</h1>
                <p class="text-xl mb-8 slide-up">Find answers to common questions about PLASCHEMA and our healthcare plans.</p>
            </div>
        </div>
    </section>

    <!-- FAQ Categories -->
    <x-section spacing="py-8">
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('faqs.index') }}" class="px-4 py-2 rounded-full {{ !$currentCategory ? 'bg-plaschema text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }} font-medium">All</a>
            
            @foreach($categories as $category)
                <a href="{{ route('faqs.index', ['category' => $category]) }}" class="px-4 py-2 rounded-full {{ $currentCategory == $category ? 'bg-plaschema text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }} font-medium">{{ $category }}</a>
            @endforeach
        </div>
    </x-section>

    <!-- FAQ Items -->
    <x-section spacing="py-8 mb-16">
        <div class="max-w-3xl mx-auto">
            <div class="space-y-6">
                @forelse($faqs as $faq)
                    <div class="bg-white rounded-lg shadow-md p-6 slide-up">
                        <button class="flex justify-between items-center w-full text-left" onclick="toggleFaq('faq-{{ $faq->id }}')">
                            <h3 class="text-xl font-bold">{{ $faq->question }}</h3>
                            <svg id="faq-{{ $faq->id }}-icon" class="w-6 h-6 transform rotate-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="faq-{{ $faq->id }}-content" class="mt-4 text-gray-600 hidden">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-600">No FAQs found for this category. Please check another category or check back later.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-12 text-center">
                <p class="text-lg text-gray-600 mb-4">Didn't find the answer you're looking for?</p>
                <x-button href="{{ route('contact') }}" variant="primary" class="px-6 py-3">Contact Us</x-button>
            </div>
        </div>
    </x-section>
@endsection

@push('scripts')
<script>
    function toggleFaq(id) {
        const content = document.getElementById(`${id}-content`);
        const icon = document.getElementById(`${id}-icon`);
        
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
</script>
@endpush 