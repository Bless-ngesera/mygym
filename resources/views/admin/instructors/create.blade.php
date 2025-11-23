<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Register Instructor</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm bg-white rounded shadow">Back to Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                    <div class="mb-4 p-3 bg-purple-200 text-yellow-900 rounded">
                    <a href="{{ route('admin.instructors.index') }}"class="text-sm text-gray-600 hover:underline">
                        View All Instructors
                    </a>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.instructors.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Register a New Instructor</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input name="name" value="{{ old('name') }}" placeholder="Full name" required class="px-3 py-2 border rounded" />
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="Email" required class="px-3 py-2 border rounded" />
                        <input name="phone" value="{{ old('phone') }}" placeholder="Phone (optional)" class="px-3 py-2 border rounded" />
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            setTimeout(() => {
                const alert = document.querySelector('.bg-green-100');
                if (alert) alert.style.display = 'none';
            }, 4000); // hides after 4 seconds
        </script>
    @endpush

</x-app-layout>
