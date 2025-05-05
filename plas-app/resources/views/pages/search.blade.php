<x-app-layout>
    <div class="py-12 bg-gradient-to-b from-green-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Advanced Search</h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Find healthcare providers, news articles, and FAQs across our platform with our powerful search tools.
                </p>
            </div>

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <form action="{{ route('search.results') }}" method="GET" class="space-y-6">
                        <!-- Search Box -->
                        <div>
                            <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Search Term</label>
                            <input type="text" name="q" id="q" 
                                placeholder="Enter keywords to search..." 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Content Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Content Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="all">All Content</option>
                                    <option value="providers">Healthcare Providers</option>
                                    <option value="news">News Articles</option>
                                    <option value="faqs">FAQs</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location Filter (for Providers) -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <span class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    Search
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-12 text-center">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Popular Searches</h2>
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <a href="{{ route('search.results', ['q' => 'vaccination']) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors duration-200">Vaccination</a>
                    <a href="{{ route('search.results', ['q' => 'diabetes']) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors duration-200">Diabetes Care</a>
                    <a href="{{ route('search.results', ['q' => 'registration']) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors duration-200">Registration</a>
                    <a href="{{ route('search.results', ['q' => 'benefits']) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors duration-200">Benefits</a>
                    <a href="{{ route('search.results', ['q' => 'pediatric']) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200 transition-colors duration-200">Pediatric Care</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 