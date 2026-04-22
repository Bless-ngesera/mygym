<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">System Health</h2>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">PHP Version</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $phpVersion ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Laravel Version</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $laravelVersion ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Server Software</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $serverSoftware ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Memory Limit</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $memoryLimit ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Max Execution Time</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $maxExecutionTime ?? 'Unknown' }} seconds</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Upload Max Filesize</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $uploadMaxFilesize ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Post Max Size</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ $postMaxSize ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Database Connection</h3>
                            <p class="text-xl font-bold {{ str_contains($dbConnection ?? '', 'Failed') ? 'text-red-600' : 'text-green-600' }} mt-1">
                                {{ $dbConnection ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600">Memory Usage</h3>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ number_format(($memoryUsage ?? 0) / 1024 / 1024, 2) }} MB</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form action="{{ route('admin.system.clear-cache') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Clear System Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
