@extends('layouts.app')

@section('title', 'News Detail')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <div class="mb-3">
                    <a href="{{ route('news') }}" class="text-white hover:text-plaschema flex items-center slide-up">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to News
                    </a>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold mb-4 text-white slide-up">
                    @if($slug == 'new-partnership-announced')
                        New Partnership Announced with Healthcare Providers
                    @elseif($slug == 'enrollment-drive-success')
                        Enrollment Drive Records Significant Success
                    @elseif($slug == 'new-health-benefits-added')
                        New Health Benefits Added to PLASCHEMA Plans
                    @else
                        {{ ucwords(str_replace('-', ' ', $slug)) }}
                    @endif
                </h1>
                <div class="flex items-center slide-up">
                    <span class="text-sm text-gray-300">April 15, 2023</span>
                    <span class="mx-3">|</span>
                    <span class="text-sm text-gray-300">PLASCHEMA News</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <x-section>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="mb-8 rounded-lg overflow-hidden">
                    <img src="{{ asset('images/news-1.jpg') }}" alt="News Image" class="w-full h-auto" loading="lazy">
                </div>
                
                @if($slug == 'new-partnership-announced')
                    <div class="prose max-w-none">
                        <p class="lead">PLASCHEMA has signed a new partnership agreement with 25 additional healthcare providers across Plateau State, significantly expanding access to quality healthcare for enrollees.</p>
                        
                        <p>The Plateau State Contributory Healthcare Management Agency (PLASCHEMA) is pleased to announce the expansion of its healthcare provider network through a new partnership agreement signed today. This strategic collaboration will add 25 new healthcare facilities to PLASCHEMA's network, bringing the total number of accredited providers to over 200 across the state.</p>
                        
                        <p>The Executive Director of PLASCHEMA, Dr. John Doe, expressed enthusiasm about the partnership during the signing ceremony held at the agency's headquarters in Jos.</p>
                        
                        <blockquote>
                            <p>"This partnership represents a significant milestone in our mission to ensure that every resident of Plateau State has access to quality healthcare services. By expanding our provider network, we are bringing healthcare closer to the people and reducing the travel burden on our enrollees, especially those in rural communities."</p>
                        </blockquote>
                        
                        <h2>Expanded Healthcare Access</h2>
                        
                        <p>The new healthcare providers include primary healthcare centers, secondary facilities, and specialized care centers distributed across all 17 Local Government Areas of Plateau State. This expansion is particularly significant for enrollees in previously underserved areas who will now have more convenient access to PLASCHEMA-accredited facilities.</p>
                        
                        <p>The partnership agreement outlines strict quality standards that all new providers must maintain, ensuring that enrollees receive consistent, high-quality care regardless of which facility they visit. PLASCHEMA will conduct regular quality assurance assessments to monitor compliance with these standards.</p>
                        
                        <h2>Benefits for Enrollees</h2>
                        
                        <p>This expansion offers several advantages to PLASCHEMA enrollees:</p>
                        
                        <ul>
                            <li>Reduced travel time to access healthcare services</li>
                            <li>More options for specialized care in various medical fields</li>
                            <li>Shorter waiting times due to the increased capacity of the provider network</li>
                            <li>Enhanced emergency response capabilities in more locations</li>
                        </ul>
                        
                        <p>The new providers will begin accepting PLASCHEMA enrollees effective May 1, 2023. A complete list of all accredited facilities will be available on the PLASCHEMA website and at all PLASCHEMA offices throughout the state.</p>
                        
                        <h2>Future Plans</h2>
                        
                        <p>This partnership is part of PLASCHEMA's broader strategy to continually improve healthcare access and quality for residents of Plateau State. The agency plans to further expand its provider network in the coming years, with a particular focus on bringing specialized services to rural areas.</p>
                        
                        <p>For more information about PLASCHEMA's provider network or to enroll in a healthcare plan, please contact our office or visit our website.</p>
                    </div>
                @elseif($slug == 'enrollment-drive-success')
                    <div class="prose max-w-none">
                        <p class="lead">PLASCHEMA's recent enrollment drive has exceeded expectations, with over 50,000 new enrollees joining the various healthcare plans in just three months.</p>
                        
                        <p>The Plateau State Contributory Healthcare Management Agency (PLASCHEMA) is delighted to announce the successful conclusion of its recent enrollment campaign, which has resulted in a record number of new participants joining the state's healthcare plans.</p>
                        
                        <p>The three-month enrollment drive, which targeted both the formal and informal sectors, has added over 50,000 new enrollees to PLASCHEMA's growing membership base. This represents a 33% increase in total enrollment, bringing the total number of covered individuals to approximately 150,000.</p>
                        
                        <h2>Campaign Strategy</h2>
                        
                        <p>The campaign employed a multi-faceted approach to reach potential enrollees across Plateau State:</p>
                        
                        <ul>
                            <li>Community outreach programs in all 17 Local Government Areas</li>
                            <li>Partnerships with community leaders and traditional institutions</li>
                            <li>Media campaigns across radio, television, and digital platforms</li>
                            <li>Enrollment desks at major public events and marketplaces</li>
                            <li>Door-to-door enrollment in select communities</li>
                        </ul>
                        
                        <p>This comprehensive strategy allowed PLASCHEMA to reach diverse population segments, including those in remote areas who had previously had limited access to information about healthcare coverage options.</p>
                        
                        <h2>Sector Distribution</h2>
                        
                        <p>The enrollment growth was distributed across different sectors as follows:</p>
                        
                        <ul>
                            <li>Formal Sector: 15,000 new enrollees</li>
                            <li>Informal Sector: 28,000 new enrollees</li>
                            <li>BHCPF Program: 7,000 new enrollees</li>
                        </ul>
                        
                        <p>The significant growth in the informal sector enrollment is particularly encouraging, as this segment has traditionally been more challenging to reach with health insurance programs.</p>
                        
                        <blockquote>
                            <p>"We are extremely pleased with the results of this enrollment drive," said Mrs. Jane Smith, Director of Operations at PLASCHEMA. "The enthusiastic response from citizens across all sectors demonstrates a growing awareness of the importance of healthcare coverage and the value that PLASCHEMA plans provide."</p>
                        </blockquote>
                        
                        <h2>Impact on Healthcare Delivery</h2>
                        
                        <p>With the increased enrollment, PLASCHEMA is working closely with healthcare providers to ensure that the expanded membership base continues to receive prompt, quality care. Additional resources have been allocated to support provider facilities, particularly in areas with the highest enrollment growth.</p>
                        
                        <p>The agency has also enhanced its customer service capabilities to efficiently handle the increased volume of inquiries and support requests from new enrollees.</p>
                        
                        <h2>Future Enrollment Initiatives</h2>
                        
                        <p>Building on the success of this campaign, PLASCHEMA plans to launch targeted enrollment initiatives throughout the year, focusing on specific sectors and geographic areas with potential for further growth. The agency aims to achieve 70% healthcare coverage across Plateau State by 2025.</p>
                        
                        <p>For individuals who missed the recent enrollment drive, regular enrollment remains open at all PLASCHEMA offices and enrollment centers throughout the state.</p>
                    </div>
                @else
                    <div class="prose max-w-none">
                        <p class="lead">This is a sample news article. The actual content would be dynamically loaded based on the article slug.</p>
                        
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec nulla quis nisl dignissim tincidunt. Nulla facilisi. Fusce at magna eu ligula malesuada aliquam. Sed vel tincidunt neque. Donec vitae convallis sapien, vel efficitur nisl. Sed pulvinar, sem at eleifend sagittis, est libero sodales purus, nec efficitur est massa a tellus.</p>
                        
                        <p>Nullam vitae lacinia nisi. Aenean tempor neque vitae eros placerat, eu suscipit turpis ultrices. Fusce eu nibh ac risus ultricies hendrerit. Nulla facilisi. Nullam sodales egestas ex, sed commodo sapien tempus eu. Nullam vulputate odio ut magna elementum, id vehicula felis malesuada.</p>
                        
                        <h2>Subheading Example</h2>
                        
                        <p>Duis at blandit diam. Nullam venenatis sapien quis tortor rhoncus, non finibus nunc pretium. Nullam a tempus eros. Phasellus at finibus neque. In hac habitasse platea dictumst. Curabitur vel felis at libero malesuada bibendum.</p>
                        
                        <blockquote>
                            <p>"This is an example of a quote that might be included in a news article. Quotes from key stakeholders add credibility and human interest to news stories."</p>
                        </blockquote>
                        
                        <p>Morbi convallis, enim eu scelerisque venenatis, nunc libero lacinia sapien, in lacinia risus ipsum id enim. Aenean sagittis consequat diam, in dignissim sapien efficitur sed. Donec luctus nunc eget orci efficitur, in porttitor eros euismod.</p>
                    </div>
                @endif
                
                <!-- Tags -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Healthcare</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">PLASCHEMA</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Plateau State</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Health Insurance</span>
                    </div>
                </div>
                
                <!-- Share -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Share this article:</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.054 10.054 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 .4C4.698.4.4 4.698.4 10s4.298 9.6 9.6 9.6 9.6-4.298 9.6-9.6S15.302.4 10 .4zm3.905 7.864c.004.082.005.164.005.244 0 2.5-1.901 5.381-5.379 5.381a5.335 5.335 0 01-2.898-.85c.147.018.298.025.451.025.887 0 1.704-.301 2.351-.809a1.895 1.895 0 01-1.767-1.312 1.9 1.9 0 00.853-.033 1.892 1.892 0 01-1.517-1.854v-.023c.255.141.547.227.857.237a1.89 1.89 0 01-.585-2.526 5.376 5.376 0 003.897 1.977 1.891 1.891 0 013.222-1.725 3.797 3.797 0 001.2-.459 1.9 1.9 0 01-.831 1.047 3.799 3.799 0 001.086-.299 3.834 3.834 0 01-.943.979z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-bold mb-4">Recent News</h3>
                    <div class="space-y-4">
                        <a href="{{ route('news.detail', 'new-partnership-announced') }}" class="flex items-start group">
                            <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
                                <img src="{{ asset('images/news-1.jpg') }}" alt="News" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <div class="ml-4">
                                <h4 class="font-medium group-hover:text-plaschema transition-colors">New Partnership Announced</h4>
                                <span class="text-sm text-gray-500">April 15, 2023</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('news.detail', 'enrollment-drive-success') }}" class="flex items-start group">
                            <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
                                <img src="{{ asset('images/news-2.jpg') }}" alt="News" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <div class="ml-4">
                                <h4 class="font-medium group-hover:text-plaschema transition-colors">Enrollment Drive Success</h4>
                                <span class="text-sm text-gray-500">March 22, 2023</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('news.detail', 'new-health-benefits-added') }}" class="flex items-start group">
                            <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
                                <img src="{{ asset('images/news-3.jpg') }}" alt="News" class="w-full h-full object-cover" loading="lazy">
                            </div>
                            <div class="ml-4">
                                <h4 class="font-medium group-hover:text-plaschema transition-colors">New Health Benefits Added</h4>
                                <span class="text-sm text-gray-500">February 9, 2023</span>
                            </div>
                        </a>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <x-button href="{{ route('news') }}" variant="text" class="text-sm">View All News</x-button>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Press Releases</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">12</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Events & Activities</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">8</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Health Updates</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">15</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Policy Changes</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">6</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Community Outreach</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">10</span>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </x-section>
    
    <!-- Related Articles -->
    <x-section 
        background="bg-light-gray" 
        title="Related Articles"
        subtitle="More news and updates from PLASCHEMA"
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <x-card 
                title="Healthcare Awareness Campaign Launched" 
                image="{{ asset('images/news-4.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">PLASCHEMA launches statewide awareness campaign to educate citizens about healthcare benefits.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">January 17, 2023</span>
                    <x-button href="{{ route('news.detail', 'healthcare-awareness-campaign') }}" variant="text" class="flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>

            <x-card 
                title="Annual Performance Review" 
                image="{{ asset('images/news-5.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">PLASCHEMA releases annual performance report showing significant growth in healthcare coverage.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">December 10, 2022</span>
                    <x-button href="{{ route('news.detail', 'annual-performance-review') }}" variant="text" class="flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>

            <x-card 
                title="New Healthcare Facilities Added" 
                image="{{ asset('images/news-6.jpg') }}"
                animation="slide-up"
            >
                <p class="text-gray-600 mb-4">PLASCHEMA expands its network with the addition of 15 new healthcare facilities across the state.</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">November 5, 2022</span>
                    <x-button href="{{ route('news.detail', 'new-healthcare-facilities') }}" variant="text" class="flex items-center">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </x-button>
                </div>
            </x-card>
        </div>
    </x-section>
@endsection 