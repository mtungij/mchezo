<div class="p-4 sm:p-6 w-full max-w-full lg:max-w-full mx-auto 
            bg-white dark:bg-gray-900 shadow rounded-xl mt-6 sm:mt-10 border border-gray-200 dark:border-gray-800">

    <!-- Title -->
    <h2 class="text-2xl font-bold mb-6 text-left text-gray-900 dark:text-gray-100">
        Create New Group
    </h2>

    @if(session()->has('success'))
        <div class="bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 
                    p-3 mb-4 rounded-lg border border-cyan-300 dark:border-cyan-700">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="createGroup" class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Group Name -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                Group Name
            </label>

            <div class="relative">
            

                <input type="text" wire:model="name"
                       class="w-full pl-10 border rounded-lg p-3 bg-white dark:bg-gray-800 
                              border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                              focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none"
                       placeholder="Enter group name">
            </div>

            @error('name') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Description -->
        <div class="lg:col-span-2">
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                Description (Optional)
            </label>

            <textarea wire:model="description"
                      class="w-full border rounded-lg p-3 bg-white dark:bg-gray-800 
                             border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                             focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none"
                      placeholder="Enter description"></textarea>
        </div>

        <!-- Contribution Amount -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                   Kiasi cha Mchango (kwa kila mwanachama)
            </label>

            <div class="relative">
              
                <input type="number" wire:model="contribution_amount"
                       class="w-full pl-10 border rounded-lg p-3 bg-white dark:bg-gray-800 
                              border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                              focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none"
                       placeholder="Enter amount">
            </div>

            @error('contribution_amount') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Start Date -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                 Tarehe ya Kuanza
            </label>

            <div class="relative">
             

                <input type="date" wire:model="start_date"
                       class="w-full pl-10 border rounded-lg p-3 bg-white dark:bg-gray-800 
                              border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                              focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none">
            </div>

            @error('start_date') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Frequency Type -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                 Aina ya Mara kwa Mara
            </label>

            <select wire:model="frequency_type"
                    class="w-full border rounded-lg p-3 bg-white dark:bg-gray-800 
                           border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                           focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none">
                <option value="day">SIKU</option>
                <option value="week">WIKI</option>
                <option value="month">MWEZI</option>
            </select>
        </div>

        <!-- Interval -->
        <div>
            <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-200">
                Interval (every n days/weeks/months)
            </label>

            <input type="number" wire:model="interval" min="1"
                   class="w-full border rounded-lg p-3 bg-white dark:bg-gray-800 
                          border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                          focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none">
            @error('interval') 
                <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Submit Button -->
<div class="flex justify-center">
    <button type="submit"
        class="bg-cyan-600 dark:bg-cyan-600 text-white py-3 px-8 rounded-lg font-semibold
               hover:bg-cyan-500 transition shadow-md">
       Unda Kikundi
    </button>
</div>


    </form>

    <!-- Invite Link -->
    @if($group)
        {{-- <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg space-y-3">
            <p class="font-semibold text-gray-900 dark:text-gray-100">
                Invite Link:
            </p>

            <input type="text"
                   value="{{ url('/invite/'.$group->invite_code) }}"
                   readonly
                   class="w-full p-3 border rounded-lg bg-white dark:bg-gray-700 
                          border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                          focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none">

            <button onclick="navigator.clipboard.writeText('{{ url('/invite/'.$group->invite_code) }}')"
                    class="w-full bg-cyan-400 text-white p-3 rounded-lg hover:bg-cyan-500 transition shadow">
                Copy Link
            </button>
        </div> --}}


        <div x-data="{ copied: false }" class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg space-y-3">
    <p class="font-semibold text-gray-900 dark:text-gray-100">
        Invite Link:
    </p>

    <input type="text"
           value="{{ url('/invite/'.$group->invite_code) }}"
           readonly
           class="w-full p-3 border rounded-lg bg-white dark:bg-gray-700 
                  border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100
                  focus:border-cyan-400 focus:ring focus:ring-cyan-300/40 outline-none">

    <button 
        @click="navigator.clipboard.writeText('{{ url('/invite/'.$group->invite_code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
        :class="copied ? 'bg-green-500 hover:bg-green-600' : 'bg-cyan-400 hover:bg-cyan-500'"
        class="w-full text-white p-3 rounded-lg transition-shadow shadow-md">
        
        <span x-show="!copied" x-transition> Nakili Kiungo </span>
        <span x-show="copied" x-transition> Copied! </span>
    </button>
</div>

    @endif

</div>
