  <div class="-m-1.5 w-full">
          <div class="p-1.5 w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-gray-800 dark:border-gray-700">
              <!-- Header -->
              <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Users
                  </h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Add Member, edit and more.
                  </p>
                </div>

                <div>
                  <div class="inline-flex gap-x-2">
                    {{-- <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800" href="#">
                      View all me
                    </a> --}}

                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="#">
                      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                      </svg>
                      Add Member
                    </a>
                  </div>
                </div>
              </div>
              <!-- End Header -->

              <!-- Table -->
                @if($group->groupMembers->isEmpty())
            <p class="text-gray-700 dark:text-gray-300">No members have joined yet.</p>
        @else
                <form wire:submit.prevent="saveOrder">
              <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    

                    <th scope="col" class="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          S/No
                        </span>
                      </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Name
                        </span>
                      </div>
                    </th>

                    

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Login Code
                        </span>
                      </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Order Position
                        </span>
                      </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Amount Due
                        </span>
                      </div>
                    </th>


                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Amount Paid
                        </span>
                      </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Status
                        </span>
                      </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Payout Date
                        </span>
                      </div>
                    </th>


                    <th scope="col" class="px-6 py-3 text-start">
                      <div class="flex items-center gap-x-2">
                        <span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">
                          Pay
                        </span>
                      </div>
                    </th>

                  

                    <th scope="col" class="px-6 py-3 text-end"></th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
               

                       @foreach($this->getPayoutSchedule() as $member)

                  <tr>
                              <td class="size-px whitespace-nowrap">
  <div class="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
    <div class="flex items-center gap-x-3">
   
      <div class="grow">
        <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $member['order_position'] }}</span>

      </div>
    </div>
  </div>
</td>

                  <td class="size-px whitespace-nowrap">
  <div class="ps-6 lg:ps-3 xl:ps-0 pe-6 py-3">
    <div class="flex items-center gap-x-3">
@if ($member['passport'])
    <img src="{{ asset('storage/' . $member['passport']) }}"
         class="w-10 h-10 rounded-full object-cover"
         alt="Passport">
@else
      <img src="{{ asset('assets/images/user.png') }}"
         class="w-10 h-10 rounded-full object-cover"
         alt="Passport">
@endif






           
      <div class="grow">
<span class="block text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize">
    {{ $member['name'] }}
</span>

        <span class="block text-sm uppercase font-semibold text-gray-800 dark:text-gray-200">{{ $member['phone'] }}</span>
      </div>
    </div>
  </div>
</td>

                    <td class="h-px w-72 whitespace-nowrap">
                      <div class="px-6 py-3">
                        <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $member['login_code'] }}</span>
            
                      </div>
                    </td>
                    <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-3">
                              <input type="number"
                           min="1"
                           max="{{ $group->groupMembers->count() }}"
                           wire:model.defer="membersOrder.{{ $member['id'] }}"
                           class="w-16 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded p-1 text-center">
                        </span>
                      </div>
                    </td>
                    <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-3">
                        <div class="flex items-center gap-x-3">
                          <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ number_format($member['amount_due'], 2) }}</span>
                          <div class="flex w-full h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                            <div class="flex flex-col justify-center overflow-hidden bg-gray-800 dark:bg-gray-200" role="progressbar" style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </td> 
                    <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-3">
                        <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200"> {{ number_format($member['amount_paid'], 2) }}</span>
                      </div>
                    </td>

                      <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-3">

                          @if($member['is_paid'])
                      
                         <span class="text-sm text-gray-800 dark:text-gray-100">Amechangiwa</span>
                    @else
                      
                         <span class="text-sm text-gray-800 dark:text-gray-100">Bado Hajachangiwa</span>
                    @endif
                       
                      </div>

                        

                           <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-3">
                        <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $member['pay_date'] }}</span>
                      </div>
                    </td>

                    </td>
                    <td class="size-px whitespace-nowrap">
                      <div class="px-6 py-1.5">
                          <button type="button"
                            wire:click="openPaymentModal({{ $member['id'] }})"
                            class="bg-green-600 text-white px-2 py-1 rounded"
                            @if($memberToPay->id != $member['id']) disabled @endif>
                        Pay Member
                    </button>
                      </div>
                    </td>
                  </tr>

                  

                 
                 @endforeach
                
                </tbody>
              </table>


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

   <button type="submit"
                        class="mt-4 bg-blue-600 dark:bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                    Save Order
                </button>
            </form>
        @endif
              <!-- End Table -->

              <!-- Footer -->
              <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-800 dark:text-gray-200">12</span> results
                  </p>
                </div>

                <div>
                  <div class="inline-flex gap-x-2">
                    <button type="button" class="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800">
                      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                      </svg>
                      Prev
                    </button>

                    <button type="button" class="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800">
                      Next
                      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <!-- End Footer -->
            </div>
          </div>
        </div>