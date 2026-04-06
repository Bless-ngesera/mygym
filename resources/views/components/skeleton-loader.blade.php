@props(['type' => 'card', 'count' => 1])

@if($type === 'card')
    @for($i = 0; $i < $count; $i++)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 animate-pulse">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
            </div>
            <div class="mt-4">
                <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
    @endfor
@elseif($type === 'chart')
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 animate-pulse">
        <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
        <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded"></div>
    </div>
@elseif($type === 'table')
    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden animate-pulse">
        <div class="p-6 space-y-4">
            @for($i = 0; $i < $count; $i++)
                <div class="flex items-center space-x-4">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                </div>
            @endfor
        </div>
    </div>
@elseif($type === 'stat-card')
    <div class="bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-6 animate-pulse">
        <div class="flex justify-between">
            <div class="space-y-2">
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-24"></div>
                <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
            </div>
            <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-xl"></div>
        </div>
    </div>
@endif
