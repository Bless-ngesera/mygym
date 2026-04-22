<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">System Logs</h2>
            <div class="flex gap-2">
                <form action="{{ route('admin.system.clear-logs') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to clear all logs?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Clear Logs</button>
                </form>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Back to Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(isset($logSize))
                        <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                            <p class="text-sm text-gray-600">Log File Size: <strong>{{ $logSize }} KB</strong></p>
                        </div>
                    @endif

                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-300 font-mono" style="max-height: 500px; overflow-y: auto;">
@if(count($logs ?? []) > 0)
@foreach($logs as $log)
{{ $log }}
@endforeach
@else
No logs found. The system is running smoothly!
@endif
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
