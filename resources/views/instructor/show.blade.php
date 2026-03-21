cat > resources/views/instructor/show.blade.php << 'EOF'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Class Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('schedule.index') }}" class="text-blue-500 hover:underline">&larr; Back to Upcoming Classes</a>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Class Information</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Class Type:</p>
                            <p class="font-semibold">{{ $scheduledClass->classType->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Instructor:</p>
                            <p class="font-semibold">{{ $scheduledClass->instructor->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Date:</p>
                            <p class="font-semibold">{{ $scheduledClass->date_time->format('F j, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Time:</p>
                            <p class="font-semibold">{{ $scheduledClass->date_time->format('g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status:</p>
                            <p class="font-semibold">
                                @if($scheduledClass->date_time->isPast())
                                    <span class="text-gray-500">Past</span>
                                @else
                                    <span class="text-green-500">Upcoming</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <a href="{{ route('schedule.edit', $scheduledClass->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</a>
                        <form action="{{ route('schedule.destroy', $scheduledClass->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
EOF
