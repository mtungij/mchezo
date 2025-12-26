<x-layouts.app>
    <x-slot name="title">Group Member Registered</x-slot>

    <div class="py-8">
        @session('success')
            <div class="p-4 bg-green-100 text-green-800 rounded mb-6">
                {{ $value }}
            </div>
        @endsession
        
      <!-- Parent wrapper must have dark class on <html> or <body> -->
<div class="max-w-lg mx-auto p-8 text-center rounded-2xl shadow-xl
            bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600
            dark:from-orange-700 dark:via-orange-800 dark:to-orange-900
            text-white">

    <h1 class="text-3xl font-extrabold mb-4">
      Usajili Umefanikiwa
    </h1>

    <p class="mb-6 leading-relaxed text-white/95 dark:text-white/90">
        Hongera <span class="font-semibold">{{ $user->name }}</span>, <br>
        Umefanikiwa kujiunga na kikundi cha
        <span class="font-semibold">{{ $group->name }}</span>.
        <br><br>

        <span class="block">
            📞 Nambari ya simu:
            <span class="font-semibold">{{ $user->phone }}</span>
        </span>

        <span class="block mt-2">
            🔐 Login Code:
            <span class="font-semibold">{{ $user->login_code }}</span>
        </span>

        <span class="block mt-4 text-sm text-white/90 dark:text-white/80">
            Taarifa hizi ndizo utazitumia kuingia kwenye akaunti yako.
        </span>
    </p>

    <a href="{{ route('home') }}"
       class="inline-block px-6 py-3 rounded-full font-semibold shadow-md
              bg-white text-orange-600
              hover:bg-orange-100
              dark:bg-gray-900 dark:text-orange-400
              dark:hover:bg-gray-800
              transition duration-300">
        Go to Home
    </a>
</div>


        <div class="mt-8 text-center">
            {{-- <h2 class="text-lg">
                Group Imformation: {{ $group->name }} (ID: {{ $group->id }})    
            </h2> --}}

        {{-- <h2 class="text-lg">
                User Imformation: {{ $user->name }} (ID: {{ $user->id }})    
            </h2>

                  <h2 class="text-lg">
                User Phone: {{ $user->phone }}    
            </h2>


                  <h2 class="text-lg">
                User Logincode: {{ $user->login_code }}    
            </h2> --}}

</div>
</x-layouts.app>