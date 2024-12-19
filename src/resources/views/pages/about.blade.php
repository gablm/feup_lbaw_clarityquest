@extends('layouts.app')

@section('title')
    About Us
@endsection

@section('content')
@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'About Us', 'url' => route('about-us')]
    ];
@endphp


<div class="container mx-auto p-4">
    {!! breadcrumbs($crumbs) !!}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">About Us</h2>
        <p class="text-gray-700 mb-4">
            Welcome to ClarityQuest! We are dedicated to providing the best service possible.            Our mission is to create a platform where users can share knowledge and help each other.
        </p>
        <p class="text-gray-700 mb-4">
            Thank you for visiting our site. We hope you find it useful and engaging.
        </p>
        <p class="text-gray-700 mb-4">
            This project was developed by three students of L.EIC: Beatriz Ferreira, Gabriel Lima and Sara Cortez for LBAW 2024
        </p>
    </div>
</div>
@endsection