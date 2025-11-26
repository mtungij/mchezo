<div class="p-6 max-w-lg mx-auto bg-white dark:bg-gray-900 shadow rounded-lg mt-10">

    <h2 class="text-2xl font-bold mb-4 text-center text-gray-900 dark:text-gray-100">
        Create New Group
    </h2>

    @if(session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="createGroup">

        <!-- Group Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Group Name</label>
            <input type="text" wire:model="name"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                   placeholder="Enter group name">
            @error('name') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Description (Optional)</label>
            <textarea wire:model="description"
                      class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                      placeholder="Enter description"></textarea>
        </div>

        <!-- Contribution Amount -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Contribution Amount (per member)</label>
            <input type="number" wire:model="contribution_amount"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                   placeholder="Enter amount each member will contribute">
            @error('contribution_amount') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Start Date -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Start Date</label>
            <input type="date" wire:model="start_date"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
            @error('start_date') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Frequency Type -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Frequency Type</label>
            <select wire:model="frequency_type"
                    class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month">Month</option>
            </select>
        </div>

        <!-- Interval -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Interval (every n days/weeks/months)</label>
            <input type="number" wire:model="interval" min="1"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
            @error('interval') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-600 dark:bg-blue-500 text-white py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-600">
            Create Group
        </button>
    </form>

    <!-- Invite Link Display -->
    @if($group)
        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded">
            <p class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Invite Link:</p>

            <input type="text"
                   value="{{ url('/invite/'.$group->invite_code) }}"
                   readonly
                   class="w-full p-2 border rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">

            <button onclick="navigator.clipboard.writeText('{{ url('/invite/'.$group->invite_code) }}')"
                    class="mt-2 bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700 dark:hover:bg-green-600">
                Copy Link
            </button>
        </div>
    @endif

</div>
