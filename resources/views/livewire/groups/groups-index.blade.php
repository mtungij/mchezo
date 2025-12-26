<div>
   <div class="p-6 max-w-3xl mx-auto bg-white dark:bg-gray-900 shadow rounded-lg mt-10">
    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Your Groups</h2>

    @if($groups->isEmpty())
        <p class="text-gray-700 dark:text-gray-300">You haven't created any groups yet.</p>
    @else
        <ul class="space-y-2">
            @foreach($groups as $group)
                <li class="flex justify-between items-center p-3 border rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div>
                        <span class="font-medium">{{ $group->name }}</span>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Owner: {{ $group->owner->name ?? '—' }} Phone Number:{{ $group->owner->phone }}</div>
                    </div>
                    <a href="{{ route('groups.members', $group->id) }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                        View Members
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

</div>
