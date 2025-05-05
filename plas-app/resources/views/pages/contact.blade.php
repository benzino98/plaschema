@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">Contact Us</h1>
                <p class="text-xl mb-8 slide-up">We're here to help. Reach out to us with any questions about our healthcare plans.</p>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <x-section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <x-card 
                title="Visit Us" 
                animation="slide-up"
                class="text-center"
                icon='<svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>'
            >
                <address class="not-italic">
                    <p class="mb-2">PLASCHEMA Headquarters</p>
                    <p class="mb-2">Jos, Plateau State</p>
                    <p>Nigeria</p>
                </address>
            </x-card>

            <x-card 
                title="Call Us" 
                animation="slide-up"
                class="text-center"
                icon='<svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>'
            >
                <p class="mb-2">Main Office: +234 700-700-1111</p>
                <p class="mb-2">Customer Care: +234 700-700-1111</p>
               
            </x-card>

            <x-card 
                title="Email Us" 
                animation="slide-up"
                class="text-center"
                icon='<svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>'
            >
                <p class="mb-2">General Inquiries: info@plaschema.pl.gov.ng</p>
                <p class="mb-2">Support: support@plaschema.pl.gov.ng</p>
                <p>Media: media@plaschema.pl.gov.ng</p>
            </x-card>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center">Send Us a Message</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <form action="{{ route('contact.store') }}" method="POST" data-validate="true">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" data-type="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="phone" name="phone" data-type="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('phone') border-red-500 @enderror" value="{{ old('phone') }}" required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('category_id') border-red-500 @enderror" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('subject') border-red-500 @enderror" value="{{ old('subject') }}" required>
                    @error('subject')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-plaschema focus:border-plaschema @error('message') border-red-500 @enderror" required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="text-center">
                    <x-button type="submit" class="px-8 py-3">Send Message</x-button>
                </div>
            </form>
        </div>
    </x-section>
    
    <!-- Map Section -->
    <section class="bg-gray-50 py-16">
        <div class="container-custom">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="aspect-w-16 aspect-h-9">
                    <!-- Google Maps Embed -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d252149.61338331932!2d8.739369957068796!3d9.932885904183888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1052ea82e8732969%3A0xba26a19f2f8df626!2sJos%2C%20Plateau%20State%2C%20Nigeria!5e0!3m2!1sen!2sus!4v1718624431841!5m2!1sen!2sus"
                        class="w-full h-full border-0"
                        style="min-height: 400px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Map showing PLASCHEMA location in Jos, Plateau State">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Office Hours -->
    <x-section
        title="Office Hours"
        subtitle="Visit us during our business hours for in-person assistance."
    >
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Main Office</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span>Monday - Friday</span>
                            <span class="font-semibold text-plaschema">8:00 AM - 4:00 PM</span>
                        </li>
                      
                        <li class="flex justify-between">
                            <span>Weekends</span>
                            <span class="font-semibold">Closed</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Customer Service Center</h3>
                    <ul class="space-y-2">
                        <li class="flex justify-between">
                            <span>Monday - Sunday</span>
                            <span class="font-semibold text-plaschema">24 Hrs Services</span>
                        </li>
                      
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-center text-gray-600">Our toll-free customer service line (0700-700-1111) is available 24/7 for emergencies.</p>
            </div>
        </div>
    </x-section>
@endsection 