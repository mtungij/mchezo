<div class="p-6 sm:p-8 max-w-lg mx-auto bg-white dark:bg-gray-900 shadow-lg rounded-2xl mt-10 relative">

    <h2 class="text-3xl sm:text-4xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100">
        Jiunge na {{ $group->name }}
    </h2>

    @if(session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 mb-6 rounded-lg shadow-sm transition duration-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Maelezo ya Michango -->
    <div class="mb-6 space-y-2">
        <p class="text-gray-800 dark:text-gray-100 text-sm sm:text-base">
            Kiasi cha Michango kwa kila mshiriki: 
            <strong class="text-blue-600 dark:text-blue-400">Tsh {{ number_format($group->contribution_amount, 2) }}</strong>
        </p>


        @php
    switch (strtolower($group->frequency_type)) {
        case 'day':
            $freq = 'Siku';
            break;
        case 'week':
            $freq = 'Wiki';
            break;
        case 'month':
            $freq = 'Mwezi';
            break;
        default:
            $freq = $group->frequency_type;
    }
@endphp

<p class="text-gray-800 dark:text-gray-100 text-sm sm:text-base">
    kila: <strong>{{ $freq }}</strong>
    <br>
    siku/wiki/mwezi ngapi: <strong>{{ $group->interval }}</strong>
</p>

    </div>

    <form wire:submit.prevent="register" enctype="multipart/form-data" class="space-y-5 relative">

        <!-- Jina Kamili -->
        <div>
            <label class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Jina Kamili</label>
            <input type="text" wire:model="name"
                   class="w-full border rounded-lg p-3 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                   placeholder="Andika jina lako kamili">
            @error('name') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Namba ya Simu -->
        <div>
            <label class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Namba ya Simu</label>
            <input type="text" wire:model="phone"
                   class="w-full border rounded-lg p-3 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                   placeholder="Andika namba yako ya simu bila 0 mwanzo (mfano: 712345678)">
            @error('phone') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Picha ya Passport -->
        <div>
            <label class="block mb-2 font-semibold text-gray-800 dark:text-gray-200">Picha ya Passport</label>
            <input type="file" wire:model="passport"
                   accept="image/*" capture="user"
                   class="w-full border rounded-lg p-3 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 cursor-pointer focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Chagua picha ya uso wako (camera ya mbele itafunguka kwenye simu).</p>
            @error('passport') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror

            <!-- Orodhesha & Thumbnail -->
            @if ($passport)
                <div class="mt-4 flex flex-col sm:flex-row items-center gap-4">
                    <div>
                        <p class="text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Muonekano Kamili:</p>
                        <img src="{{ $passport->temporaryUrl() }}" alt="Passport Preview" class="w-40 h-40 object-cover rounded-lg border shadow-sm">
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-200 mb-1 text-sm sm:text-base">Thumbnail:</p>
                        @php
                            $thumbnail = $passport->temporaryUrl();
                        @endphp
                        <img src="{{ $thumbnail }}" alt="Passport Thumbnail" class="w-20 h-20 object-cover rounded-lg border shadow-sm">
                    </div>
                </div>
            @endif
        </div>

        <!-- Kitufe cha Kutuma -->
        <button type="submit"
                class="w-full bg-blue-600 dark:bg-blue-500 text-white font-semibold py-3 rounded-lg flex justify-center items-center gap-3 hover:bg-blue-700 dark:hover:bg-blue-600 transition transform hover:scale-105 relative">
            <!-- Spinner ndani ya kitufe -->
            <svg wire:loading wire:target="register" class="animate-spin h-5 w-5 text-white absolute left-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>

            <!-- Maneno ya Kitufe -->
            <span>
                <span wire:loading.remove wire:target="register">Jiunge na Kikundi</span>
                <span wire:loading wire:target="register">Ina jiunga...</span>
            </span>
        </button>
    </form>

    <!-- Loader ya Skrini Nzima -->
    <div wire:loading wire:target="register" class="fixed inset-0 bg-white/70 dark:bg-gray-900/70 z-50 flex items-center justify-center">
        <div class="flex flex-col items-center gap-4">
            <svg class="animate-spin h-12 w-12 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span class="text-gray-700 dark:text-gray-200 font-medium text-lg">Inatuma usajili wako...</span>
        </div>
    </div>

</div>
