<div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10" 
     x-data="{ showModal: false, copied: false }" 
     @group-created.window="showModal = true">
    <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl 
                border border-gray-200 dark:border-gray-800 p-5 sm:p-8">

        <!-- Title -->
        <h2 class="text-xl sm:text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
            Tengeneza Kikundi Kipya
        </h2>

        @if(session()->has('success'))
            <div class="mb-5 p-4 rounded-lg 
                        bg-cyan-100 dark:bg-cyan-900/40 
                        text-cyan-700 dark:text-cyan-300 
                        border border-cyan-300 dark:border-cyan-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="createGroup"
              class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Group Name -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                    Jina la Kikundi
                </label>
                <input type="text" wire:model="name"
                    class="w-full rounded-lg p-3 
                           bg-white dark:bg-gray-800 
                           border border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none"
                    placeholder="Enter group name">
                @error('name')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contribution Amount -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                    Kiasi cha Mchango (kwa kila mwanachama)
                </label>
                <input type="number" wire:model="contribution_amount"
                    class="w-full rounded-lg p-3 
                           bg-white dark:bg-gray-800 
                           border border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none"
                    placeholder="Enter amount">
                @error('contribution_amount')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Start Date -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                    Tarehe ya Kuanza
                </label>
                <input type="date" wire:model="start_date"
                    class="w-full rounded-lg p-3 
                           bg-white dark:bg-gray-800 
                           border border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none">
                @error('start_date')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Frequency Type -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                    Aina ya Mara kwa Mara
                </label>
                <select wire:model="frequency_type"
                    class="w-full rounded-lg p-3 
                           bg-white dark:bg-gray-800 
                           border border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none">
                    <option value="day">SIKU</option>
                    <option value="week">WIKI</option>
                    <option value="month">MWEZI</option>
                </select>
            </div>

            <!-- Interval -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                    Interval
                </label>
                <input type="number" min="1" wire:model="interval"
                    class="w-full rounded-lg p-3 
                           bg-white dark:bg-gray-800 
                           border border-gray-300 dark:border-gray-600
                           text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none">
                @error('interval')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full sm:w-auto mx-auto block
                           bg-cyan-600 hover:bg-cyan-500 
                           text-white font-semibold
                           px-10 py-3 rounded-xl
                           transition shadow-md">
                    Unda Kikundi
                </button>
            </div>
        </form>

    </div>

    <!-- Modal for Invite Link -->
@if($group)
<div x-data="{ showModal: true, copied: false }" 
     x-show="showModal"
     x-cloak
     @click.self="showModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4 relative">

        <!-- Close Button -->
        <button @click="showModal = false"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Modal Title -->
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            Kikundi Kimeundwa!
        </h3>

        <p class="text-gray-700 dark:text-gray-300">
            Kiungo cha Mwaliko
        </p>

        <!-- Invite Link Input -->
        <input type="text"
               readonly
               value="{{ url('/invite/'.$group->invite_code) }}"
               class="w-full rounded-lg p-3
                      bg-gray-100 dark:bg-gray-800
                      border border-gray-300 dark:border-gray-600
                      text-gray-900 dark:text-gray-100">

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-3">
            
            <!-- Copy Button -->
            <button
                @click="navigator.clipboard.writeText('{{ url('/invite/'.$group->invite_code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                :class="copied ? 'bg-green-500 hover:bg-green-600' : 'bg-cyan-500 hover:bg-cyan-600'"
                class="py-3 rounded-lg text-white font-semibold transition shadow flex items-center justify-center gap-2">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span x-show="!copied">Copy</span>
                <span x-show="copied">Copied!</span>
            </button>

            <!-- WhatsApp Share Button -->
            <a href="https://wa.me/?text={{ urlencode('Jiunge na kikundi changu cha mchezo: ' . url('/invite/'.$group->invite_code)) }}"
               target="_blank"
               class="py-3 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold transition shadow flex items-center justify-center gap-2">
                
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span>Share</span>
            </a>

        </div>
    </div>
</div>
@endif

</div>
