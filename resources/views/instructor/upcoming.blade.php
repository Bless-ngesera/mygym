<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upcoming Classes
        </h2>
    </x-slot>

    <div class="py-12"
      style="background-image: url('{{ asset('images/background2.jpg') }}'); 
      background-size: cover; 
      background-position: center; 
      background-attachment: fixed;">
      
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                <div class="p-6 text-gray-900 max-w-xl divide-y">
                    @forelse ($scheduledClasses as $class)
                  <div class="py-6">
                     <div class="flex gap-6 justify-between">
                        <div>
                           <p class="text-2xl font-bold text-purple-700">{{ $class->classType->name }}</p>
                           <span class="text-slate-600 text-sm">{{ $class->classType->minutes }} minutes</span>
                        </div>
                        <div class="text-right flex-shrink-0">
                           <p class="text-lg font-bold">{{ $class->date_time->format('g:i a') }}</p>
                           <p class="text-sm">{{ $class->date_time->format('jS M') }}</p>
                        </div>
                     </div>
                     <div class="mt-1 text-right">
                        <form method="post" action="{{ route('schedule.destroy', $class) }}">
                           @csrf
                           @method('DELETE')
                           <x-danger-button class="px-3 py-1">Cancel</x-danger-button>
                        </form>
                     </div>
                  </div>
                  @empty
                  <div>
                     <p>You don't have any upcoming classes</p>
                     <a class="inline-block mt-6 underline text-sm" href="{{ route('schedule.create') }}">
                        Schedule now
                     </a>
                  </div>
                  @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>