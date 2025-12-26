<x-layouts.app>
    <x-slot name="title">Group Member Registered</x-slot>

    <div class="py-8">
        @session('success')
            <div class="p-4 bg-green-100 text-green-800 rounded mb-6">
                {{ $value }}
            </div>
        @endsession
        
        <div class="max-w-lg mx-auto p-6 text-center rounded-lg shadow-md ">
            <h1 class="text-2xl font-bold mb-4">Registration Successful!</h1>
            <p class="mb-6">You have been successfully registered as a member of the group.</p>
            <a href="{{ route('home') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Go to Home
            </a>
        </div>

        <div class="mt-8 text-center">
            <h2 class="text-lg">
                Group Imformation: {{ $group->name }} (ID: {{ $group->id }})    
            </h2>

        <h2 class="text-lg">
                User Imformation: {{ $user->name }} (ID: {{ $user->id }})    
            </h2>

                  <h2 class="text-lg">
                User Phone: {{ $user->phone }}    
            </h2>


                  <h2 class="text-lg">
                User Logincode: {{ $user->login_code }}    
            </h2>

</div>
</x-layouts.app>