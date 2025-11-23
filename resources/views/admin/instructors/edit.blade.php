<x-app-layout>
    <x-slot name="header">
        <h2>Edit Instructor</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.instructors.update', $instructor->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input name="name" value="{{ old('name', $instructor->name) }}" required class="w-full px-3 py-2 border rounded mb-3" />
            <input name="email" type="email" value="{{ old('email', $instructor->email) }}" required class="w-full px-3 py-2 border rounded mb-3" />

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.instructors.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
