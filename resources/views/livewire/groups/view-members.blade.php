
      
        <div class="w-full">
    <section class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-12">
  <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
    <!-- Heading & Filters -->
    <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
      <div>
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
              <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
              
                Groups
              </a>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                </svg>
                <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white md:ms-2">Group List</a>
              </div>
            </li>
           
          </ol>
        </nav>
        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl"> {{ $this->group->name }} Members</h2>
      </div>
  
    </div>


<div class="flex flex-wrap items-center gap-3 mb-4">

    <!-- Search Input -->
  <!-- Only search -->
<input type="text"
       wire:model.live="search"
       placeholder="Search name, phone, login code..."
       class="w-full sm:w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">


</div>



<!-- CARD GRID -->

  <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">

@foreach ($this->getPayoutSchedule() as $member)



<div class="w-full">
  <div class="flex flex-col md:flex-row md:justify-between md:items-start md:space-x-3">


    <!-- Customer Card -->
  <div class="w-full sm:w-full md:w-full lg:w-auto mb-4">


     <div class="bg-white p-4 border-t-4 border-green-500 rounded-lg shadow-md w-full h-auto">

    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-green-400">
    <img src="{{ $member['passport'] ? asset('storage/' . $member['passport']) : asset('assets/images/user.png') }}"
         class="w-full h-full object-cover"
         alt="Passport">
</div>

        <h1 class="text-green-600 font-bold text-xl text-center uppercase whitespace-nowrap overflow-hidden truncate">
    {{ $member['name'] }}
        </h1>
        {{-- <h2 class="text-sm text-green-500 text-center font-semibold">memberToPay-?id</h2> --}}
        <p class="text-center mt-2 text-gray-800 font-medium">{{ $member['phone'] }}</p>

        {{-- <div class="mt-4 text-center">
  <a href="" 
     class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all">
     📩 Tuma SMS ya Malipo
  </a>
</div> --}}

     <div class="mt-4 text-center">
  <a class="font-semibold {{ $member['is_paid'] ? 'inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all' : 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all' }}">
                    {{ $member['is_paid'] ? 'Amechangiwa' : 'Bado Hajachangiwa' }}
 </a>
</div>






        <ul class="mt-5 bg-gray-100 text-gray-700 divide-y divide-gray-300 rounded-lg shadow-sm text-sm">
          <li class="flex items-center justify-between py-2 px-3">
            <span class="font-bold text-base">Order Position</span>
            <span class="px-3 py-1 rounded-full text-xs font-medium >Active</span>
          </li>

              

          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span></span><span>{{ $member['order_position'] }}</span></li>
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Login Code</span><span>{{ $member['login_code'] }}</span></li>
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Amount Due</span><span>  {{ number_format($member['amount_due'], 2) }}</span></li>
            <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Amount Paid</span><span>{{ number_format($member['amount_paid'], 2) }}</span></li>
      @php
                $percent = $member['amount_due'] > 0
                    ? ($member['amount_paid'] / $member['amount_due']) * 100
                    : 0;
            @endphp

            <div class="mt-2">
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-green-500 h-2.5 rounded-full"
                         style="width: {{ $percent }}%"></div>
                </div>
                <p class="text-xs mt-1 text-gray-600 dark:text-gray-300">
                    {{ round($percent) }}% Paid
                </p>
            </div>
            <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Tarehe Ya Kupewa</span><span>{{ $member['pay_date'] }}</span></li>


           
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Badilisha Mpangilio</span><span>
            
            <input type="number"
                min="1"
                max="{{ $group->groupMembers->count() }}"
                wire:model.defer="membersOrder.{{ $member['id'] }}"
                class="mt-1 w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded p-2 text-center">
          
          </span></li>

        </ul>

         <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">📎 changia member:</h3>
                <div class="flex flex-col gap-2 text-sm">



                  @if($showPaymentModal && $memberToPay)
<div class="fixed inset-0 flex items-center justify-center z-50 bg-black/60 backdrop-blur-sm p-4">

    <button
    wire:click="closePaymentModal"
    class="absolute top-1 right-1 md:top-2 md:right-2 text-gray-500 hover:text-gray-700 
           dark:text-gray-300 dark:hover:text-white transition"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
    <!-- Modal Box -->
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg md:max-w-2xl p-6 animate-[fadeIn_0.3s_ease] border border-gray-200 dark:border-gray-700">

        <!-- Close Button -->
       <!-- Close Button -->



        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
            Pay to: {{ $memberToPay->user->name ?? '-' }}
        </h3>

        @php
            $remainingAmount = $group->contribution_amount - \App\Models\Payment::where('group_id', $group->id)
                ->where('member_id', $memberToPay->id)
                ->where('payer_id', $payerId ?? 0)
                ->sum('amount');
        @endphp

        <!-- Payer Dropdown -->
        <div class="mb-5">
            <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
                Select Payer
            </label>

            <select wire:model="payerId"
                class="w-full border rounded-xl p-3 select2payer text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition">
                <option value="">-- Select Member --</option>

                @foreach($group->groupMembers as $member)
                    @php
                        $alreadyPaidByThisPayer = \App\Models\Payment::where('group_id', $group->id)
                            ->where('member_id', $memberToPay->id)
                            ->where('payer_id', $member->id)
                            ->sum('amount');

                        $remainingForThisMember = $group->contribution_amount - $alreadyPaidByThisPayer;
                    @endphp

                    @if($remainingForThisMember > 0)
                        <option value="{{ $member->id }}">
                            {{ $member->user->name ?? '-' }}
                            (Paid: {{ number_format($alreadyPaidByThisPayer, 2) }} /
                             Remaining: {{ number_format($remainingForThisMember, 2) }})
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Amount Input -->
        <div class="mb-6">
            <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
                Amount (Tsh)
            </label>

            <input type="number"
                   wire:model="paymentAmount"
                   class="w-full border rounded-xl p-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition"
                   max="{{ $remainingAmount }}"
                   placeholder="Enter amount"
                   @if($remainingAmount <= 0) disabled @endif>

        
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <button wire:click="closePaymentModal"
                class="px-4 py-2 rounded-xl bg-gray-500 dark:bg-gray-700 text-white hover:bg-gray-600 dark:hover:bg-gray-600 transition">
                Cancel
            </button>

            <button wire:click="processPayment"
                class="px-4 py-2 rounded-xl bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition"
                @if($remainingAmount <= 0) disabled @endif>
                Pay
            </button>
        </div>

    </div>
</div>
@endif
                
                                                 
<button type="button"
                wire:click="openPaymentModal({{ $member['id'] }})"
                class="bg-green-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                Pay Member
            </button>
                         
                         
                  
                </div>
            </div>
        
      </div>
    </div>
    </div>
    </div>

@endforeach
</div>

   
    <div class="w-full text-center">
      <button type="button" wire:click="saveOrder" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">Save Order</button>
    </div>
  </div>
  

</section>
      </div>

















 