@extends('frontend.layout.home')

@php
    $hero_card = [
        [
            'title' => 'Property Management',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Tenancies',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Contacts',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],

    ];
    $features_list = [
        [
            'title' => 'Manage properties',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Tenancies',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Contacts',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Bookings',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Bookings',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Bookings',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Bookings',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
        [
            'title' => 'Manage Bookings',
            'details' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
        ],
    ];
@endphp

@section('content')
<section class="hero_section px-4">
        <div class="main_hero_section">
            <div class="">
                <div class="hero_title">Property Management <br>
                    <span>Web Framework</span>
                </div>
                <p class="hero_description">Use the timeline view to map out the big picture, communicate updates to stakeholders, and ensure your team stays on the same page.</p>
                <div class="hero_btns">
                    <button class="btn btn_primary">Get Started</button>
                    <button class="btn btn_secondary">Book a Demo</button>
                </div>
            </div>
            <div class="">
                <img src="{{ asset('storage/uploads/all/hero_img.webp') }}" alt="Hero" class="img-fluid">
            </div>
        </div>
        
        <div class="hero_cards">
            @foreach ($hero_card as $card)
                <div class="hero_card_item"> 
                    <div class="card_title">{{ $card['title'] }}</div>
                    <p>{{ $card['details'] }}</p>
                </div>
            @endforeach
        </div>

        
        <div class="features_list">
            <div class="text-center my-3">
                <h2>Enable features as you grow</h2>
        </div>
            <div class="carousel_wrapper">
                <div class="carousel">
                    @foreach ($features_list as $feature)
                        <div class="carousel-item feature_item {{ $loop->first ? 'active' : '' }}">
                            <div class="card_title">{{ $feature['title'] }}</div>
                            <p>{{ $feature['details'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
</section>
<section class="features_section">
    <div class="features_item bg_white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <img src="{{ asset('storage/uploads/all/hero_img.webp') }}" alt="Feature" class="img-fluid">
                </div>
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <div class="feature_card ">
                        <h3>Customize how your team’s work flows</h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="features_item">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <div class="feature_card">
                        <h3>Customize how your team’s work flows</h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    </div>
                </div>
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <img src="{{ asset('storage/uploads/all/hero_img.webp') }}" alt="Feature" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    <div class="features_item bg_white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <img src="{{ asset('storage/uploads/all/hero_img.webp') }}" alt="Feature" class="img-fluid">
                </div>
                <div class="col-md-6 col-12 mb-md-0 mb-3">
                    <div class="feature_card">
                        <h3>Customize how your team’s work flows</h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="book_demo_section">
    <div class="container">
            <div class="">
                <h2>Book a demo now</h2>
                <p>Use the timeline view to map out the big picture, communicate updates to stakeholders, and ensure your team stays on the same page.</p>
            </div>
            <div class="book_demo_btn">
                <button class="btn btn_secondary btn-lg">Book Now</button>
             </div>
            <div class="book_demo_img">
                <img src="{{ asset('storage/uploads/all/hero_img.webp') }}" alt="Book a demo" class="">
            </div>
    </div>
</section>
@endsection
