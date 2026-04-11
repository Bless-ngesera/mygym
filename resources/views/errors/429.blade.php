@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen py-12 bg-gray-100 dark:bg-gray-900">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-purple-600">429</h1>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mt-4">Too Many Requests</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">You have sent too many messages. Please wait a moment before trying again.</p>
        <div class="mt-6">
            <a href="{{ url()->previous() }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
