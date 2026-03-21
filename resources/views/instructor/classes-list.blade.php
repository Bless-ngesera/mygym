cat > resources/views/instructor/classes-list.blade.php << 'EOF'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Classes') }}
        </h2>
    </x-slot>

    <div class="py-12"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">All Scheduled Classes</h3>
                        <a href="{{ route('schedule.create') }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                            + Schedule New Class
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            @if(session('error'))
                                {{ session('error') }}
                            @else
                                <ul class="list-disc pl-5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white/80 rounded-lg">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-3 px-4 text-left">Class Type</th>
                                    <th class="py-3 px-4 text-left">Date & Time</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classes as $class)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $class->classType->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $class->date_time->format('M d, Y h:i A') }}</td>
                                    <td class="py-3 px-4">
                                        @if($class->date_time->isPast())
                                            <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-xs">Past</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-200 text-green-700 rounded-full text-xs">Upcoming</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('schedule.edit', $class->id) }}" class="text-blue-500 hover:underline mr-3">Edit</a>
                                        <form action="{{ route('schedule.destroy', $class->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No classes scheduled yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $classes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
EOF
