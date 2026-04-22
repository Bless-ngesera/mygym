<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Queue Status</h2>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800">Pending Jobs</h3>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $pendingJobs ?? 0 }}</p>
                            <p class="text-sm text-blue-600 mt-1">Jobs waiting to be processed</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-red-800">Failed Jobs</h3>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ $failedJobs ?? 0 }}</p>
                            <p class="text-sm text-red-600 mt-1">Jobs that failed to process</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form action="{{ route('admin.system.queue-restart') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                Restart Queue Worker
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
