@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-custom">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 animate-on-scroll" data-animation="slide-up">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">Healthcare for All Citizens</h1>
                    <p class="text-xl mb-8">The Plateau State Contributory Healthcare Management Agency provides accessible and affordable healthcare for citizens across the state.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('plans') }}" class="btn-white hover-shadow-md button-push">View Health Plans</a>
                        <a href="{{ route('about') }}" class="btn-outline hover-shadow-sm button-push">Learn More</a>
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-12 animate-on-scroll" data-animation="fade-in" data-delay="200">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Healthcare Services" class="rounded-lg shadow-xl hover-lift" width="600" height="400" loading="lazy">
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
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 animate-on-scroll" data-animation="slide-up">
                <!-- Building Office Icon -->
                <div class="flex mb-4 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-plaschema" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Formal Sector</h3>
                <p class="text-gray-600 mb-4">A comprehensive health insurance plan for employees in the formal sector, covering individuals and families.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 animate-on-scroll" data-animation="slide-up">
                <!-- Shopping Bag Icon -->
                <div class="flex mb-4 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-plaschema" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Informal Sector</h3>
                <p class="text-gray-600 mb-4">Tailored health coverage for traders, artisans, and other workers in the informal economy.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 animate-on-scroll" data-animation="slide-up">
                <!-- Heart Icon -->
                <div class="flex mb-4 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-plaschema" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">BHCPF</h3>
                <p class="text-gray-600 mb-4">Basic Healthcare Provision Fund for vulnerable populations, ensuring essential health services.</p>
            </div>

            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 animate-on-scroll" data-animation="slide-up">
                <!-- User Group Icon -->
                <div class="flex mb-4 items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-plaschema" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Equity Program</h3>
                <p class="text-gray-600 mb-4">Healthcare support for the poorest and most vulnerable groups in Plateau State.</p>
            </div>
        </div>
    </x-section>

    <!-- Statistics Section -->
    <x-section 
        background="bg-plaschema-DEFAULT text-white" 
        title="Enrollment Statistics"
        subtitle="Making healthcare accessible to thousands of citizens across Plateau State."
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-center">
            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Users Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-enrolled">{{ isset($statistics['total_count']) ? number_format($statistics['total_count']) : '0' }}</div>
                <div class="text-lg">Total Enrollments</div>
            </div>

            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Building Office Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-formal">{{ isset($statistics['formal_count']) ? number_format($statistics['formal_count']) : '0' }}</div>
                <div class="text-lg">Formal Enrollments</div>
            </div>

            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Shopping Bag Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-informal">{{ isset($statistics['total_informal_count']) ? number_format($statistics['total_informal_count']) : '0' }}</div>
                <div class="text-lg">Informal Enrollments</div>
            </div>

            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Heart Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-bhcpf">{{ isset($statistics['bhcpf_count']) ? number_format($statistics['bhcpf_count']) : '0' }}</div>
                <div class="text-lg">BHCPF Enrollments</div>
            </div>

            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- User Group Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-equity">{{ isset($statistics['equity_count']) ? number_format($statistics['equity_count']) : '0' }}</div>
                <div class="text-lg">Equity Enrollments</div>
            </div>
            
            <!-- New card for Principals -->
            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- User Icon -->
                <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-principals">{{ isset($statistics['principals_count']) ? number_format($statistics['principals_count']) : '0' }}</div>
                <div class="text-lg">Principal Enrollments</div>
            </div>
            
            <!-- New card for Spouses -->
            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Two People Icon -->
              
                 <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>


                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-spouses">{{ isset($statistics['spouse_count']) ? number_format($statistics['spouse_count']) : '0' }}</div>
                <div class="text-lg">Spouse Enrollments</div>
            </div>
            
            <!-- New card for Children -->
            <div class="bg-black/30 rounded-lg p-4 shadow-lg border border-white/20 hover:bg-black/40 transition-colors fade-in">
                <!-- Child Icon -->
               
                 <div class="flex justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>


                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-children">{{ isset($statistics['children_count']) ? number_format($statistics['children_count']) : '0' }}</div>
                <div class="text-lg">Child Enrollments</div>
            </div>
        </div>
        
       
    </x-section>

    <!-- Latest News Section -->
    <x-section 
        title="Latest News & Updates" 
        subtitle="Stay informed about the latest developments at PLASCHEMA."
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($latestNews as $news)
                <x-card 
                    title="{{ $news->title }}" 
                    image="{{ $news->image_path_medium ? ImageHelper::formatPath($news->image_path_medium) : asset('images/placeholder.svg') }}"
                    animation="slide-up"
                >
                    <p class="text-gray-600 mb-4">{{ $news->excerpt }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $news->published_at->format('F d, Y') }}</span>
                        <x-button href="{{ route('news.show', $news->slug) }}" variant="text" class="flex items-center text-plaschema">
                            Read More
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </x-button>
                    </div>
                </x-card>
            @empty
                <div class="col-span-3 text-center py-8">
                    <p class="text-gray-500">No news articles available at this time.</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-12">
            <x-button href="{{ route('news') }}" variant="secondary" class="bg-plaschema-dark text-white hover:bg-plaschema">View All News</x-button>
        </div>
    </x-section>
@endsection

@push('scripts')
<script>
    // Global variable to store our statistics
    let cachedStatistics = {
        'total_count': {{ isset($statistics['total_count']) ? $statistics['total_count'] : 0 }},
        'formal_count': {{ isset($statistics['formal_count']) ? $statistics['formal_count'] : 0 }},
        'total_informal_count': {{ isset($statistics['total_informal_count']) ? $statistics['total_informal_count'] : 0 }},
        'bhcpf_count': {{ isset($statistics['bhcpf_count']) ? $statistics['bhcpf_count'] : 0 }},
        'equity_count': {{ isset($statistics['equity_count']) ? $statistics['equity_count'] : 0 }},
        'principals_count': {{ isset($statistics['principals_count']) ? $statistics['principals_count'] : 0 }},
        'spouse_count': {{ isset($statistics['spouse_count']) ? $statistics['spouse_count'] : 0 }},
        'children_count': {{ isset($statistics['children_count']) ? $statistics['children_count'] : 0 }}
    };

    // Track which stats have been animated
    let animatedStats = {
        'stat-enrolled': false,
        'stat-formal': false,
        'stat-informal': false,
        'stat-bhcpf': false,
        'stat-equity': false,
        'stat-principals': false,
        'stat-spouses': false,
        'stat-children': false
    };

    // Enrollment statistics background refresh
    document.addEventListener('DOMContentLoaded', function() {
        // Set up background refresh every 5 minutes (adjust as needed)
        setInterval(refreshStatistics, 5 * 60 * 1000);
        
        function refreshStatistics() {
            fetch('{{ route('refresh-statistics') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        // Update our cached values
                        cachedStatistics = {
                            'total_count': data.data.total_count,
                            'formal_count': data.data.formal_count,
                            'total_informal_count': data.data.total_informal_count,
                            'bhcpf_count': data.data.bhcpf_count,
                            'equity_count': data.data.equity_count,
                            'principals_count': data.data.principals_count,
                            'spouse_count': data.data.spouse_count,
                            'children_count': data.data.children_count
                        };
                        
                        // Update the statistics with smooth counting animation
                        animateStatUpdate('stat-enrolled', data.data.total_count);
                        animateStatUpdate('stat-formal', data.data.formal_count);
                        animateStatUpdate('stat-informal', data.data.total_informal_count);
                        animateStatUpdate('stat-bhcpf', data.data.bhcpf_count);
                        animateStatUpdate('stat-equity', data.data.equity_count);
                        animateStatUpdate('stat-principals', data.data.principals_count);
                        animateStatUpdate('stat-spouses', data.data.spouse_count);
                        animateStatUpdate('stat-children', data.data.children_count);
                    }
                })
                .catch(error => console.error('Error refreshing statistics:', error));
        }
        
        function animateStatUpdate(id, newValue) {
            const el = document.getElementById(id);
            const currentValue = parseInt(el.textContent.replace(/,/g, ''));
            const diff = newValue - currentValue;
            
            // If no change or element doesn't exist, do nothing
            if (diff === 0 || !el) return;
            
            const duration = 1000; // ms
            const frameDuration = 1000/60; // 60fps
            const totalFrames = Math.round(duration / frameDuration);
            let frame = 0;
            
            const counter = setInterval(() => {
                frame++;
                const progress = frame / totalFrames;
                const currentCount = Math.floor(currentValue + diff * progress);
                
                if (frame === totalFrames) {
                    clearInterval(counter);
                }
                
                el.textContent = currentCount.toLocaleString();
            }, frameDuration);
        }
        
        // Initial counter animation for statistics when they come into view
        setupStatisticsAnimation();
    });
    
    // Function to set up the animation for statistics
    function setupStatisticsAnimation() {
        const statElements = [
            'stat-enrolled', 'stat-formal', 'stat-informal', 'stat-bhcpf', 'stat-equity',
            'stat-principals', 'stat-spouses', 'stat-children'
        ];
        
        // Create a mapping of stat IDs to their respective cached values
        const statValueMap = {
            'stat-enrolled': 'total_count',
            'stat-formal': 'formal_count',
            'stat-informal': 'total_informal_count',
            'stat-bhcpf': 'bhcpf_count',
            'stat-equity': 'equity_count',
            'stat-principals': 'principals_count',
            'stat-spouses': 'spouse_count',
            'stat-children': 'children_count'
        };
        
        // Create one observer for all the statistics, triggering animations as they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const statId = entry.target.id;
                
                // Only animate if this specific stat hasn't been animated yet and is in view
                if (entry.isIntersecting && !animatedStats[statId]) {
                    // Mark this stat as animated
                    animatedStats[statId] = true;
                    
                    // Get the appropriate value from cached statistics
                    const cacheKey = statValueMap[statId];
                    const targetValue = cachedStatistics[cacheKey];
                    
                    // Start from zero
                    entry.target.textContent = '0';
                    
                    // Animate to the target value
                            animateCounter(statId, targetValue);
                }
            });
        }, { threshold: 0.1 });
        
        // Observe each statistic element individually
        statElements.forEach(statId => {
            const element = document.getElementById(statId);
            if (element) {
                observer.observe(element);
            }
        });
        
        function animateCounter(id, target) {
            const el = document.getElementById(id);
            if (!el) return;
            
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
    }
</script>
@endpush 