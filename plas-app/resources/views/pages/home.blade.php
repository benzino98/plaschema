@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 slide-up">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">Healthcare for All Citizens</h1>
                    <p class="text-xl mb-8">The Plateau State Contributory Healthcare Management Agency provides accessible and affordable healthcare for citizens across the state.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('plans') }}" class="btn-white">View Health Plans</a>
                        <a href="{{ route('about') }}" class="btn-outline">Learn More</a>
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-12 animate-on-scroll" data-animation="slide-right" data-delay="300" style="animation-duration: 800ms; transform: translateX(-40px); opacity: 0;">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Healthcare Services" class="rounded-lg shadow-xl" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Health Plans Section -->
    <x-section 
        title="Our Health Plans" 
        subtitle="We offer various healthcare plans designed to meet the needs of different sectors of the population."
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-card 
                title="Formal Sector" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <p class="text-gray-600 mb-4">A comprehensive health insurance plan for employees in the formal sector, covering individuals and families.</p>
                <x-button href="#" variant="text" class="flex items-center text-plaschema">
                    Learn More
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </x-button>
            </x-card>

            <x-card 
                title="Informal Sector" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <p class="text-gray-600 mb-4">Tailored health coverage for traders, artisans, and other workers in the informal economy.</p>
                <x-button href="#" variant="text" class="flex items-center text-plaschema">
                    Learn More
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </x-button>
            </x-card>

            <x-card 
                title="BHCPF" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <p class="text-gray-600 mb-4">Basic Healthcare Provision Fund for vulnerable populations, ensuring essential health services.</p>
                <x-button href="#" variant="text" class="flex items-center text-plaschema">
                    Learn More
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </x-button>
            </x-card>

            <x-card 
                title="Equity Program" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <p class="text-gray-600 mb-4">Healthcare support for the poorest and most vulnerable groups in Plateau State.</p>
                <x-button href="#" variant="text" class="flex items-center text-plaschema">
                    Learn More
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </x-button>
            </x-card>
        </div>
    </x-section>

    <!-- Statistics Section -->
    <x-section 
        background="bg-plaschema-dark text-white" 
        title="Our Impact"
        subtitle="Making healthcare accessible to thousands of citizens across Plateau State."
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div class="bg-black/30 rounded-lg p-6 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2" id="stat-enrolled">0</div>
                <div class="text-xl">Enrolled Citizens</div>
            </div>

            <div class="bg-black/30 rounded-lg p-6 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2" id="stat-providers">0</div>
                <div class="text-xl">Healthcare Providers</div>
            </div>

            <div class="bg-black/30 rounded-lg p-6 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2" id="stat-accredited-hmos">0</div>
                <div class="text-xl">Accredited HMOs</div>
            </div>

            <div class="bg-black/30 rounded-lg p-6 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <div class="text-4xl md:text-5xl font-bold text-white mb-2" id="stat-lgas">0</div>
                <div class="text-xl">LGAs Covered</div>
            </div>
        </div>
    </x-section>

    <!-- Latest News Section -->
    <x-section 
        title="Latest News & Updates" 
        subtitle="Stay informed about the latest developments at PLASCHEMA."
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <x-card 
                title="New Partnership Announced" 
                image="{{ asset('images/news/news-1.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">PLASCHEMA signs agreement with new healthcare providers to expand service coverage.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">April 15, 2023</span>
                    <x-button href="#" variant="text" class="flex items-center text-plaschema">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>

            <x-card 
                title="Enrollment Drive Success" 
                image="{{ asset('images/news/news-2.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">Recent enrollment campaign records significant increase in scheme participation.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">March 22, 2023</span>
                    <x-button href="#" variant="text" class="flex items-center text-plaschema">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>

            <x-card 
                title="New Health Benefits Added" 
                image="{{ asset('images/news/news-3.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">PLASCHEMA announces addition of new benefits to the healthcare packages.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">February 9, 2023</span>
                    <x-button href="#" variant="text" class="flex items-center text-plaschema">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>
        </div>
        
        <div class="text-center mt-12">
            <x-button href="{{ route('news') }}" variant="secondary" class="bg-plaschema-dark text-white hover:bg-plaschema">View All News</x-button>
        </div>
    </x-section>
@endsection

@push('scripts')
<script>
    // Simple counter animation for statistics
    document.addEventListener('DOMContentLoaded', function() {
        const stats = [
            { id: 'stat-enrolled', target: 218473},
            { id: 'stat-providers', target: 443 },
            { id: 'stat-accredited-hmos', target: 13},
            { id: 'stat-lgas', target: 17 }
        ];
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    stats.forEach(stat => {
                        if (entry.target.contains(document.getElementById(stat.id))) {
                            animateCounter(stat.id, stat.target);
                        }
                    });
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.fade-in').forEach(el => {
            if (el.contains(document.getElementById('stat-enrolled')) || 
                el.contains(document.getElementById('stat-providers')) ||
                el.contains(document.getElementById('stat-accredited-hmos')) ||
                el.contains(document.getElementById('stat-lgas'))) {
                observer.observe(el);
            }
        });
        
        function animateCounter(id, target) {
            const el = document.getElementById(id);
            const duration = 2000; // ms
            const frameDuration = 1000/60; // 60fps
            const totalFrames = Math.round(duration / frameDuration);
            let frame = 0;
            const counter = setInterval(() => {
                frame++;
                const progress = frame / totalFrames;
                const currentCount = Math.round(progress * target);
                if (frame === totalFrames) {
                    clearInterval(counter);
                }
                el.textContent = currentCount.toLocaleString();
            }, frameDuration);
        }
    });
</script>
@endpush 