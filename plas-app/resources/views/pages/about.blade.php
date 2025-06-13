@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">About PLASCHEMA</h1>
                <p class="text-xl mb-8 slide-up">The Plateau State Contributory Healthcare Management Agency was established to provide accessible and affordable healthcare for all residents of Plateau State.</p>
            </div>
        </div>
    </section>

    <!-- About Content Section -->
    <x-section>
        <div class="flex flex-col md:flex-row gap-12 items-center">
            <div class="md:w-1/2 slide-up">
                <h2 class="text-3xl font-bold mb-6 text-gray-800">Our Mission</h2>
                <p class="mb-4">To ensure that every resident of Plateau State has access to equiatble, accessible and quality healthcare services without suffering financial hardship.</p>
                <p class="mb-4">Through our various healthcare plans, we aim to provide comprehensive coverage and improve health outcomes across all communities in the state.</p>
                <h2 class="text-3xl font-bold mb-6 mt-8 text-gray-800">Our Vision</h2>
                <p>A Plateau State where all residents have access to quality healthcare, leading to improved quality of life and productivity.</p>
            </div>
            <div class="md:w-1/2 fade-in">
                <img src="{{ asset('images/about/about-image.jpg') }}" alt="PLASCHEMA Office" class="rounded-lg shadow-xl" loading="lazy">
            </div>
        </div>
    </x-section>

    <!-- Leadership Section -->
    <x-section
        background="light"
        title="Our Leadership"
        subtitle="Meet the team leading PLASCHEMA's mission to transform healthcare in Plateau State."
    >
        <!-- Director General - Featured -->
        <div class="mb-12">
            <div class="bg-gradient-to-r from-plaschema-dark/5 to-plaschema/5 p-1 rounded-xl">
                <div class="flex flex-col md:flex-row bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="md:w-1/3 h-80 md:h-auto overflow-hidden">
                        <img src="{{ asset('images/team/leader-1.jpg') }}" alt="Dr. Agabus Manasseh" 
                             class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700" 
                             loading="lazy">
                    </div>
                    <div class="md:w-2/3 p-8 flex flex-col justify-center">
                        <div class="mb-2">
                            <span class="bg-plaschema text-white text-xs px-3 py-1 rounded-full uppercase tracking-wider">Leadership</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold mb-2 text-gray-800">Dr. Agabus Manasseh</h3>
                        <p class="text-plaschema font-medium text-lg mb-4">Director General</p>
                        <div class="h-0.5 w-16 bg-plaschema mb-4"></div>
                        <p class="text-gray-600 mb-6">Leading PLASCHEMA's efforts to expand healthcare coverage across Plateau State through innovative policies and sustainable healthcare financing models.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-500 hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directors -->
        <h3 class="text-2xl font-bold text-gray-800 mb-8 text-center">Directors</h3>
        <!-- First three directors in normal grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Director 1 -->
            <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="{{ asset('images/team/leader-2.jpg') }}" alt="Dr Kwande Solomon Dawal" 
                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" 
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
                        <div class="p-4 w-full flex justify-center space-x-4">
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </a>
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Dr Kwande Solomon Dawal</h3>
                    <p class="text-plaschema font-medium mb-3">Director of Operations</p>
                    <div class="h-0.5 w-12 bg-plaschema/30 mb-4"></div>
                    <p class="text-gray-600">Overseeing the day-to-day operations and service delivery of PLASCHEMA across all local government areas.</p>
                </div>
            </div>

            <!-- Director 2 -->
            <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="{{ asset('images/team/leader-3.jpg') }}" alt="Pharm. Danladi Wuyep" 
                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" 
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
                        <div class="p-4 w-full flex justify-center space-x-4">
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </a>
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Pharm. Danladi Wuyep</h3>
                    <p class="text-plaschema font-medium mb-3">Director of Health Services, Standards and Quality Control</p>
                    <div class="h-0.5 w-12 bg-plaschema/30 mb-4"></div>
                    <p class="text-gray-600">Ensuring that healthcare services meet the highest standards across all partner facilities.</p>
                </div>
            </div>

            <!-- Director 3 -->
            <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="aspect-[4/3] overflow-hidden relative">
                    <img src="{{ asset('images/team/leader-4.jpg') }}" alt="Mrs Theresa Gwofen" 
                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" 
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
                        <div class="p-4 w-full flex justify-center space-x-4">
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </a>
                            <a href="#" class="text-white hover:text-plaschema transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1 text-gray-800">Mrs Theresa Gwofen</h3>
                    <p class="text-plaschema font-medium mb-3">Director of Marketing and Communications</p>
                    <div class="h-0.5 w-12 bg-plaschema/30 mb-4"></div>
                    <p class="text-gray-600">Leading outreach and awareness campaigns to increase enrollment across Plateau State.</p>
                </div>
            </div>
        </div>

        <!-- Last two directors centered -->
        <div class="flex justify-center">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl">
                <!-- Director 4 -->
                <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/3] overflow-hidden relative">
                        <img src="{{ asset('images/team/leader-5.jpg') }}" alt="Dr Nden Julfa" 
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" 
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
                            <div class="p-4 w-full flex justify-center space-x-4">
                                <a href="#" class="text-white hover:text-plaschema transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </a>
                                <a href="#" class="text-white hover:text-plaschema transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1 text-gray-800">Dr Nden Julfa</h3>
                        <p class="text-plaschema font-medium mb-3">Director of Planning, Research and Statistics</p>
                        <div class="h-0.5 w-12 bg-plaschema/30 mb-4"></div>
                        <p class="text-gray-600">Leading data collection and analysis to inform strategic decision-making and program development.</p>
                    </div>
                </div>

                <!-- Director 5 -->
                <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/3] overflow-hidden relative">
                        <img src="{{ asset('images/team/leader-6.jpg') }}" alt="Mrs. Altine Gongden" 
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700" 
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end">
                            <div class="p-4 w-full flex justify-center space-x-4">
                                <a href="#" class="text-white hover:text-plaschema transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </a>
                                <a href="#" class="text-white hover:text-plaschema transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1 text-gray-800">Mrs. Altine Gongden</h3>
                        <p class="text-plaschema font-medium mb-3">Director of Finance</p>
                        <div class="h-0.5 w-12 bg-plaschema/30 mb-4"></div>
                        <p class="text-gray-600">Managing financial resources and administrative functions to ensure sustainability of PLASCHEMA's programs.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-section>

    <!-- History Timeline -->
    <x-section
        title="Our History"
        subtitle="The journey of PLASCHEMA from inception to present day."
    >
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-col space-y-12">
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2019</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Establishment</h3>
                        <p>PLASCHEMA was established by the Plateau State Government to implement the State's Contributory Healthcare Scheme.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2020</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Launch of Formal Sector Plan</h3>
                        <p>PLASCHEMA launched its first healthcare plan targeting employees in the formal sector.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2021</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Expansion to Informal Sector</h3>
                        <p>Extended coverage to include workers in the informal economy, significantly increasing enrollment.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2021</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">BHCPF Implementation</h3>
                        <p>Successfully implemented the Basic Healthcare Provision Fund to provide coverage for vulnerable populations.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2025</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Equity Statewide Expansion</h3>
                        <p>Achieved coverage in all 17 Local Government Areas of Plateau State, reaching remote communities.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-section>

    <!-- Call to Action -->
    <section class="bg-plaschema-dark text-white py-16">
        <div class="container-custom text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 text-white">Join Our Healthcare Community</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">Be part of our mission to provide quality healthcare for all residents of Plateau State. Enroll in one of our healthcare plans today.</p>
            <div class="flex justify-center space-x-4">
                <x-button href="{{ route('plans') }}" class="text-lg px-6 py-3">View Health Plans</x-button>
                <x-button href="{{ route('contact') }}" variant="outline" class="text-lg px-6 py-3">Contact Us</x-button>
            </div>
        </div>
    </section>
@endsection 