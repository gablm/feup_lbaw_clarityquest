@extends('layouts.app')

@section('title')
    FAQ
@endsection

@section('content')

@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'FAQ', 'url' => route('faq')]
    ];
@endphp

<div class="container mx-auto p-4">
    
    {!! breadcrumbs($crumbs) !!}
    
    <h1 class="text-4xl font-bold mb-8 text-center">Frequently Asked Questions</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">What is this website about?</h2>
            <p class="text-gray-700">This website is a platform for users to ask questions and get answers from the community.</p>
        </div>

        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">How do I create an account?</h2>
            <p class="text-gray-700">You can create an account by clicking on the "Register" button at the top right corner of the page and filling out the registration form.</p>
        </div>

        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">How do I ask a question?</h2>
            <p class="text-gray-700">Once you are logged in, you can ask a question by clicking on the "Ask Question" button and filling out the question form.</p>
        </div>

        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">How do I answer a question?</h2>
            <p class="text-gray-700">You can answer a question by clicking on the question you want to answer and then clicking on the "Answer" button to submit your answer.</p>
        </div>

        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">How do I contact support?</h2>
            <p class="text-gray-700">You can contact support by clicking on the "Contact Us" link at the bottom of the page and filling out the contact form.</p>
        </div>

        <div class="faq-item p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">How do I reset my password?</h2>
            <p class="text-gray-700">You can reset your password by clicking on the "Forgot Password" link on the login page and following the instructions to reset your password.</p>
        </div>
    </div>
</div>
@endsection