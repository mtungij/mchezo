<div class="p-6 max-w-md mx-auto bg-white dark:bg-gray-900 shadow rounded-lg mt-10">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-900 dark:text-gray-100">
        Join {{ $group->name }}
    </h2>

    @if(session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Contribution Info -->
    <div class="mb-4">
        <p class="text-gray-800 dark:text-gray-100">
            Contribution Amount per member: <strong>Tsh {{ number_format($group->contribution_amount, 2) }}</strong>
        </p>
        <p class="text-gray-800 dark:text-gray-100">
            Frequency: <strong>{{ ucfirst($group->frequency_type) }}</strong>, Interval: <strong>{{ $group->interval }}</strong>
        </p>
    </div>

    <form wire:submit.prevent="register" enctype="multipart/form-data">
        <!-- Full Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Full Name</label>
            <input type="text" wire:model="name"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                   placeholder="Enter your full name">
            @error('name') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Phone Number -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Phone Number</label>
            <input type="text" wire:model="phone"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100"
                   placeholder="Enter your phone number">
            @error('phone') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Passport Upload -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold text-gray-800 dark:text-gray-200">Passport Photo</label>
            <input type="file" wire:model="passport"
                   accept="image/*"
                   class="w-full border rounded p-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
            @error('passport') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror

            <!-- Preview & Thumbnail -->
            @if ($passport)
                <div class="mt-2 flex items-center gap-4">
                    <div>
                        <p class="text-gray-700 dark:text-gray-200 mb-1">Full Preview:</p>
                        <img src="{{ $passport->temporaryUrl() }}" alt="Passport Preview" class="w-40 h-40 object-cover rounded">
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-200 mb-1">Thumbnail (auto-resize):</p>
                        @php
                            // Generate temporary thumbnail URL
                            $thumbnail = $passport->temporaryUrl();
                        @endphp
                        <img src="{{ $thumbnail }}" alt="Passport Thumbnail" class="w-16 h-16 object-cover rounded border">
                    </div>
                </div>
            @endif
        </div>

        <button type="submit"
                class="w-full bg-blue-600 dark:bg-blue-500 text-white py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-600">
            Join Group
        </button>
    </form>
</div>
