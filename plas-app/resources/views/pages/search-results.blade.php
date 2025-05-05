<x-app-layout>
    <div class="py-12 bg-gradient-to-b from-green-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Search Results</h1>
                
                @if($searchTerm)
                    <p class="text-lg text-gray-600">
                        Search results for: <span class="font-semibold">"{{ $searchTerm }}"</span>
                        @if($type != 'all') 
                            in <span class="font-semibold">{{ ucfirst($type) }}</span>
                        @endif
                        @if($category)
                            @php
                                $categoryName = \App\Models\Category::find($category)->name ?? '';
                            @endphp
                            @if($categoryName)
                                in category <span class="font-semibold">{{ $categoryName }}</span>
                            @endif
                        @endif
                        @if($location)
                            in <span class="font-semibold">{{ $location }}</span>
                        @endif
                    </p>
                @else
                    <p class="text-lg text-gray-600">Browsing all content with applied filters</p>
                @endif
            </div>

            <!-- Search Form -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-10">
                <div class="p-6">
                    <form action="{{ route('search.results') }}" method="GET" class="space-y-4">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-grow">
                                <input type="text" name="q" id="q" value="{{ $searchTerm }}" 
                                    placeholder="Enter keywords to search..." 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                            <div>
                                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                        Search
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Content Type Filter -->
                            <div>
                                <label for="type" class="sr-only">Content Type</label>
                                <select name="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All Content</option>
                                    <option value="providers" {{ $type == 'providers' ? 'selected' : '' }}>Healthcare Providers</option>
                                    <option value="news" {{ $type == 'news' ? 'selected' : '' }}>News Articles</option>
                                    <option value="faqs" {{ $type == 'faqs' ? 'selected' : '' }}>FAQs</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="sr-only">Category</label>
                                <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location Filter -->
                            <div>
                                <label for="location" class="sr-only">Location</label>
                                <select name="location" id="location" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" {{ $location == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filters -->
            @if($searchTerm || $type != 'all' || $category || $location)
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="text-sm font-medium text-gray-700">Active filters:</span>
                    
                    @if($searchTerm)
                        <a href="{{ route('search.results', array_merge(request()->except(['q']), ['q' => ''])) }}" 
                            class="flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            "{{ $searchTerm }}"
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                    
                    @if($type != 'all')
                        <a href="{{ route('search.results', array_merge(request()->except(['type']), ['type' => 'all'])) }}" 
                            class="flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            {{ ucfirst($type) }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                    
                    @if($category)
                        @php
                            $categoryName = \App\Models\Category::find($category)->name ?? '';
                        @endphp
                        @if($categoryName)
                            <a href="{{ route('search.results', array_merge(request()->except(['category']), ['category' => ''])) }}" 
                                class="flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                Category: {{ $categoryName }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    @endif
                    
                    @if($location)
                        <a href="{{ route('search.results', array_merge(request()->except(['location']), ['location' => ''])) }}" 
                            class="flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            Location: {{ $location }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                    
                    <a href="{{ route('search.results') }}" class="text-sm text-green-600 hover:text-green-800 hover:underline">
                        Clear all filters
                    </a>
                </div>
            @endif

            <!-- Results -->
            <div class="space-y-8">
                @if($type == 'all')
                    <!-- All Results -->
                    @if(isset($results['providers']) && $results['providers']->count() > 0)
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Healthcare Providers ({{ $results['providers']->total() }})</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($results['providers'] as $provider)
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                        @if($provider->image)
                                            <img src="{{ asset('storage/' . $provider->image) }}" alt="{{ $provider->name }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500">No Image</span>
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            <h3 class="text-xl font-semibold mb-2">{{ $provider->name }}</h3>
                                            <p class="text-gray-600 mb-3 line-clamp-2">{{ $provider->description }}</p>
                                            <a href="{{ route('providers.show', $provider->id) }}" class="text-green-600 hover:text-green-800 font-medium">View Details →</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($results['providers']->count() < $results['providers']->total())
                                <div class="mt-4 text-center">
                                    <a href="{{ route('search.results', array_merge(request()->query(), ['type' => 'providers'])) }}" class="text-green-600 hover:text-green-800 font-medium">
                                        See all {{ $results['providers']->total() }} providers →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(isset($results['news']) && $results['news']->count() > 0)
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">News Articles ({{ $results['news']->total() }})</h2>
                            <div class="space-y-4">
                                @foreach($results['news'] as $article)
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                        <div class="md:flex">
                                            @if($article->image)
                                                <div class="md:flex-shrink-0">
                                                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="h-48 w-full md:w-48 object-cover">
                                                </div>
                                            @endif
                                            <div class="p-6">
                                                <div class="flex items-center text-sm text-gray-500 mb-1">
                                                    <span>{{ $article->published_at->format('M d, Y') }}</span>
                                                    @if($article->category)
                                                        <span class="mx-2">•</span>
                                                        <span>{{ $article->category->name }}</span>
                                                    @endif
                                                </div>
                                                <h3 class="text-xl font-semibold mb-2">{{ $article->title }}</h3>
                                                <p class="text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                                                <a href="{{ route('news.show', $article->slug) }}" class="text-green-600 hover:text-green-800 font-medium">Read More →</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($results['news']->count() < $results['news']->total())
                                <div class="mt-4 text-center">
                                    <a href="{{ route('search.results', array_merge(request()->query(), ['type' => 'news'])) }}" class="text-green-600 hover:text-green-800 font-medium">
                                        See all {{ $results['news']->total() }} news articles →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(isset($results['faqs']) && $results['faqs']->count() > 0)
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">FAQs ({{ $results['faqs']->total() }})</h2>
                            <div class="space-y-4">
                                @foreach($results['faqs'] as $faq)
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                                        <h3 class="text-xl font-semibold mb-2">{{ $faq->question }}</h3>
                                        <div class="text-gray-600 prose max-w-none mb-3">
                                            {!! Str::limit(strip_tags($faq->answer), 200) !!}
                                        </div>
                                        <a href="{{ route('faqs.index') }}#faq-{{ $faq->id }}" class="text-green-600 hover:text-green-800 font-medium">Read Full Answer →</a>
                                    </div>
                                @endforeach
                            </div>
                            @if($results['faqs']->count() < $results['faqs']->total())
                                <div class="mt-4 text-center">
                                    <a href="{{ route('search.results', array_merge(request()->query(), ['type' => 'faqs'])) }}" class="text-green-600 hover:text-green-800 font-medium">
                                        See all {{ $results['faqs']->total() }} FAQs →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if((!isset($results['providers']) || $results['providers']->count() == 0) && 
                        (!isset($results['news']) || $results['news']->count() == 0) && 
                        (!isset($results['faqs']) || $results['faqs']->count() == 0))
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-2xl font-medium text-gray-900 mb-2">No results found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search terms or filters to find what you're looking for.</p>
                            <a href="{{ route('search') }}" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 inline-block">Reset Search</a>
                        </div>
                    @endif

                @elseif($type == 'providers')
                    <!-- Provider Results -->
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Healthcare Providers</h2>
                    @if($results->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($results as $provider)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    @if($provider->image)
                                        <img src="{{ asset('storage/' . $provider->image) }}" alt="{{ $provider->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">No Image</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-xl font-semibold mb-2">{{ $provider->name }}</h3>
                                        <p class="text-gray-600 mb-3 line-clamp-2">{{ $provider->description }}</p>
                                        <a href="{{ route('providers.show', $provider->id) }}" class="text-green-600 hover:text-green-800 font-medium">View Details →</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-2xl font-medium text-gray-900 mb-2">No healthcare providers found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search terms or filters.</p>
                            <a href="{{ route('search') }}" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 inline-block">Reset Search</a>
                        </div>
                    @endif

                @elseif($type == 'news')
                    <!-- News Results -->
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">News Articles</h2>
                    @if($results->count() > 0)
                        <div class="space-y-4">
                            @foreach($results as $article)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                    <div class="md:flex">
                                        @if($article->image)
                                            <div class="md:flex-shrink-0">
                                                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="h-48 w-full md:w-48 object-cover">
                                            </div>
                                        @endif
                                        <div class="p-6">
                                            <div class="flex items-center text-sm text-gray-500 mb-1">
                                                <span>{{ $article->published_at->format('M d, Y') }}</span>
                                                @if($article->category)
                                                    <span class="mx-2">•</span>
                                                    <span>{{ $article->category->name }}</span>
                                                @endif
                                            </div>
                                            <h3 class="text-xl font-semibold mb-2">{{ $article->title }}</h3>
                                            <p class="text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                                            <a href="{{ route('news.show', $article->slug) }}" class="text-green-600 hover:text-green-800 font-medium">Read More →</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-2xl font-medium text-gray-900 mb-2">No news articles found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search terms or filters.</p>
                            <a href="{{ route('search') }}" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 inline-block">Reset Search</a>
                        </div>
                    @endif

                @elseif($type == 'faqs')
                    <!-- FAQ Results -->
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">FAQs</h2>
                    @if($results->count() > 0)
                        <div class="space-y-4">
                            @foreach($results as $faq)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                                    <h3 class="text-xl font-semibold mb-2">{{ $faq->question }}</h3>
                                    <div class="text-gray-600 prose max-w-none mb-3">
                                        {!! Str::limit(strip_tags($faq->answer), 200) !!}
                                    </div>
                                    <a href="{{ route('faqs.index') }}#faq-{{ $faq->id }}" class="text-green-600 hover:text-green-800 font-medium">Read Full Answer →</a>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-2xl font-medium text-gray-900 mb-2">No FAQs found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your search terms or filters.</p>
                            <a href="{{ route('search') }}" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 inline-block">Reset Search</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 