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
                <p class="mb-4">To ensure that every resident of Plateau State has access to quality healthcare services without suffering financial hardship.</p>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow slide-up">
                <div class="overflow-hidden h-56 rounded-t-lg">
                    <img src="{{ asset('images/team/leader-1.jpg') }}" alt="Dr. John Doe" class="w-full h-full object-cover object-[center_top_-20px]" style="object-position: center 15%;" loading="lazy">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1">Dr. John Doe</h3>
                    <p class="text-gray-600 mb-4 italic">Director General</p>
                    <p class="text-gray-600">Leading PLASCHEMA's efforts to expand healthcare coverage across Plateau State.</p>
                </div>
            </div>

            <div class="card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow slide-up">
                <div class="overflow-hidden h-56 rounded-t-lg">
                    <img src="{{ asset('images/team/leader-2.jpg') }}" alt="Mrs. Jane Smith" class="w-full h-full object-cover" style="object-position: center 30%;" loading="lazy">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1">Mrs. Jane Smith</h3>
                    <p class="text-gray-600 mb-4 italic">Director of Operations</p>
                    <p class="text-gray-600">Overseeing the day-to-day operations and service delivery of PLASCHEMA.</p>
                </div>
            </div>

            <div class="card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow slide-up">
                <div class="overflow-hidden h-56 rounded-t-lg">
                    <img src="{{ asset('images/team/leader-3.jpg') }}" alt="Mr. James Johnson" class="w-full h-full object-cover" style="object-position: center 25%;" loading="lazy">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1">Mr. James Johnson</h3>
                    <p class="text-gray-600 mb-4 italic">Director of Health Services, Standards and Quality Control</p>
                    <p class="text-gray-600">Managing the financial aspects of PLASCHEMA to ensure sustainability.</p>
                </div>
            </div>

            <div class="card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow slide-up">
                <div class="overflow-hidden h-56 rounded-t-lg">
                    <img src="{{ asset('images/team/leader-4.jpg') }}" alt="Mr. Robert Williams" class="w-full h-full object-cover" style="object-position: center 20%;" loading="lazy">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1">Mr. Robert Williams</h3>
                    <p class="text-gray-600 mb-4 italic">Director of Marketing</p>
                    <p class="text-gray-600">Managing the financial aspects of PLASCHEMA to ensure sustainability.</p>
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
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2018</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Establishment</h3>
                        <p>PLASCHEMA was established by the Plateau State Government to implement the State's Contributory Healthcare Scheme.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2019</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Launch of Formal Sector Plan</h3>
                        <p>PLASCHEMA launched its first healthcare plan targeting employees in the formal sector.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2020</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Expansion to Informal Sector</h3>
                        <p>Extended coverage to include workers in the informal economy, significantly increasing enrollment.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2022</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">BHCPF Implementation</h3>
                        <p>Successfully implemented the Basic Healthcare Provision Fund to provide coverage for vulnerable populations.</p>
                    </div>
                </div>
                
                <div class="flex">
                    <div class="w-24 text-center">
                        <div class="bg-plaschema text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto">2023</div>
                    </div>
                    <div class="ml-8 slide-up">
                        <h3 class="text-xl font-bold mb-2">Statewide Expansion</h3>
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