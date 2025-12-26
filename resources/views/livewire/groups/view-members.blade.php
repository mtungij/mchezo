
      
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
        <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl"> {{ $this->group->name ?? 'Group' }} Members</h2>

        @if (session()->has('success'))
          <div x-data="{ show: true }" x-init="setTimeout(()=> show=false, 3000)" x-show="show"
               class="mt-3 mb-2 p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-700 shadow"
               role="alert">
            {{ session('success') }}
          </div>
        @endif

        @if (session()->has('error'))
          <div x-data="{ show: true }" x-init="setTimeout(()=> show=false, 4000)" x-show="show"
               class="mt-3 mb-2 p-3 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700 shadow"
               role="alert">
            {{ session('error') }}
          </div>
        @endif
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

@php $stats = $this->membersStats; @endphp

<div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4">
  <div class="p-3 bg-white rounded-lg shadow-sm text-center dark:bg-gray-800">
    <div class="text-sm text-gray-500 dark:text-gray-400">Jumla Wanachama</div>
    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
  </div>
  <div class="p-3 bg-white rounded-lg shadow-sm text-center dark:bg-gray-800">
    <div class="text-sm text-gray-500 dark:text-gray-400">Wamelipwa</div>
    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['paid'] }}</div>
    <div class="mt-2">
      <button type="button"
              wire:click="openPaymentsModal"
              class="text-xs px-2 py-1 rounded bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-200 dark:border-green-700">
        View Payments
      </button>
    </div>
  </div>
  <div class="p-3 bg-white rounded-lg shadow-sm text-center dark:bg-gray-800">
    <div class="text-sm text-gray-500 dark:text-gray-400">Hawajalipwa</div>
    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['not_paid'] }}</div>
  </div>
  <div class="p-3 bg-white rounded-lg shadow-sm text-center dark:bg-gray-800">
    <div class="text-sm text-gray-500 dark:text-gray-400">Tarehe Mwisho</div>
    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['end_date'] ? \Carbon\Carbon::parse($stats['end_date'])->format('d M, Y') : '-' }}</div>
  </div>
</div>

{{-- Hawajanilipa bado (kwa mpokeaji aliyeingia, siku yake ya malipo) --}}
  @php
      $myUnpaid = $this->myUnpaidPayers;
      $myPayDate = $this->myPayDate;
      $isMyDay = $myPayDate && \Carbon\Carbon::parse($myPayDate)->isSameDay(now());
  @endphp
  <div class="mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
      <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Hawajanilipa</h3>
          @if($isMyDay && empty($myUnpaid))
            <p class="text-sm text-green-600 dark:text-green-400 font-semibold">Tayari nimelipwa ✅</p>
          @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Orodha ya wana kikundi ambao bado hawajanilipa, itaonekana tu siku yangu ya malipo.</p>
          @endif
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200">{{ $isMyDay ? count($myUnpaid) : 0 }}</span>
      </div>
      <div class="p-4">
        @if(!$myPayDate)
          <div class="text-sm text-gray-600 dark:text-gray-300">Haijawekwa tarehe yangu ya malipo.</div>
        @elseif(!$isMyDay)
          <div class="text-sm text-gray-600 dark:text-gray-300">Leo si tarehe yangu ya malipo. Tarehe yangu ni {{ \Carbon\Carbon::parse($myPayDate)->format('d M, Y') }}.</div>
        @elseif(empty($myUnpaid))
          <div class="text-sm text-gray-600 dark:text-gray-300">Tayari nimelipwa. ✅</div>
        @else
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200">
              <thead class="text-xs uppercase bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                <tr>
                  <th class="px-4 py-3">Mlipaji</th>
                  <th class="px-4 py-3">Simu</th>
                  <th class="px-4 py-3 text-right">Kiasi Kilichobaki</th>
                </tr>
              </thead>
              <tbody>
                @foreach($myUnpaid as $row)
                  <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                    <td class="px-4 py-3">{{ $row['phone'] }}</td>
                    <td class="px-4 py-3 text-right">
                      @if(($row['remaining'] ?? 0) == 0)
                        <span class="text-green-600 dark:text-green-400 font-semibold">Tayari nimelipwa</span>
                      @else
                        <span class="text-red-600 dark:text-red-400">{{ number_format($row['remaining'] ?? 0, 2) }}</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
    </div>
  </div>
</div>



<!-- CARD GRID -->

  <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">

@foreach ($this->getPayoutSchedule() as $member)



<div class="w-full">
  <div class="flex flex-col md:flex-row md:justify-between md:items-start md:space-x-3">


    <!-- Customer Card -->
  <div class="w-full sm:w-full md:w-full lg:w-auto mb-4">


     <div class="bg-white p-4 border-t-4 border-green-500 rounded-lg shadow-md w-full h-auto dark:bg-gray-800 dark:border-green-400 dark:text-gray-100">

    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-green-400">
    <img src="{{ $member['passport'] ? asset('storage/' . $member['passport']) : asset('assets/images/user.png') }}"
         class="w-full h-full object-cover"
         alt="Passport">
</div>

        <h1 class="text-green-600 font-bold text-xl text-center uppercase whitespace-nowrap overflow-hidden truncate">
    {{ $member['name'] }}
        </h1>
        {{-- <h2 class="text-sm text-green-500 text-center font-semibold">memberToPay-?id</h2> --}}
        <p class="text-center mt-2 text-gray-800 font-medium dark:text-gray-200">{{ $member['phone'] }}</p>

        {{-- <div class="mt-4 text-center">
  <a href="" 
     class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all">
     📩 Tuma SMS ya Malipo
  </a>
</div> --}}

     <div class="mt-4 text-center">
  <a class="font-semibold {{ $member['is_paid'] ? 'inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-md transition-all' : 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-semibold rounded-lg shadow-md transition-all' }}">
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
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Contribution Amount</span><span>{{ number_format($member['amount_due'], 2) }}</span></li>
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Amount Paid</span><span class="text-green-600 dark:text-green-400">{{ number_format($member['amount_paid'], 2) }}</span></li>
          <li class="flex items-center justify-between py-2 px-3 font-bold text-base"><span>Remaining</span><span class="text-red-600 dark:text-red-400">{{ number_format($member['amount_due'] - $member['amount_paid'], 2) }}</span></li>
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
            
            @if(auth()->id() === $this->group->owner_id)
            <input type="number"
                min="1"
                max="{{ $group->groupMembers->count() }}"
                wire:model.defer="membersOrder.{{ $member['id'] }}"
                class="mt-1 w-20 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md px-2 py-1 text-center shadow-sm" />
            @else
            <span class="text-sm">Only owner can change order</span>
            @endif
          
          </span></li>

        </ul>

         <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">📎 changia member:</h3>
                <div class="flex flex-col gap-2 text-sm">


                
                                                 
@php $today = now()->format('Y-m-d'); @endphp
@php $remainingToReceive = max(0, ($member['amount_due'] ?? 0) - ($member['amount_paid'] ?? 0)); @endphp

<div class="mt-4 flex flex-col items-center gap-3">
    <div class="flex items-center gap-2">
      @if($remainingToReceive > 0 && (auth()->id() === $this->group->owner_id || (auth()->id() === $member['user_id'] && $member['can_pay'] && $member['can_pay_until'] === $today)))
        <button type="button" wire:click="openPaymentModal({{ $member['id'] }})" class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-semibold bg-green-600 text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 shadow transition" title="Pay Member" aria-label="Pay Member">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2M7 9v6a2 2 0 002 2h6a2 2 0 002-2V9M16 13a1 1 0 11-2 0 1 1 0 012 0z" />
          </svg>
          <span>Pay Member</span>
        </button>
      @elseif($remainingToReceive <= 0)
        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-semibold bg-red-600 text-white dark:bg-green-500">
          Amelipwa
        </span>
      @endif

        @if(auth()->id() === $this->group->owner_id && $member['pay_date'] === $today)
            <button wire:click="togglePaymentRights({{ $member['id'] }})"
                    title="{{ $member['can_pay'] && $member['can_pay_until'] === $today ? 'Ondoa Ruhusa' : 'Ruhusu' }}"
                    aria-label="{{ $member['can_pay'] && $member['can_pay_until'] === $today ? 'Ondoa Ruhusa' : 'Ruhusu' }}"
                    class="px-3 py-2 rounded-md bg-cyan-600 text-gray-950 font-medium transition-shadow focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $member['can_pay'] && $member['can_pay_until'] === $today ? 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-300' : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-300' }}">
                {{ $member['can_pay'] && $member['can_pay_until'] === $today ? 'Ondoa Ruhusa' : 'Ruhusu' }}
            </button>
        @endif
    </div>

    @if($member['can_pay'] && $member['can_pay_until'] === $today)
        <div class="text-xs inline-flex items-center gap-1 px-2 py-1 bg-cyan-50 text-cyan-800 rounded-full font-semibold dark:bg-cyan-900 dark:text-cyan-200 ring-1 ring-cyan-100 dark:ring-cyan-700">Ana ruhusa leo</div>
    @endif
</div>


                         
                         
                  
                </div>
            </div>
        
      </div>
    </div>
    </div>
    </div>

@endforeach
</div>

<!-- Global Payment Modal (outside loop) -->
@if($showPaymentModal && $memberToPay)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
     @click.self="wire.call('closePaymentModal')"
     @keydown.escape.window="wire.call('closePaymentModal')">
  
  <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg md:max-w-2xl p-6 border border-gray-200 dark:border-gray-700">
    
    <!-- Close Button -->
    <button wire:click="closePaymentModal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
      Pay to: {{ $memberToPay?->user?->name ?? '-' }}
    </h3>

    @if (session()->has('error'))
      <div class="mb-3 p-3 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700" role="alert">
        {{ session('error') }}
      </div>
    @endif

    @error('payment')
      <div class="mb-3 p-3 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-700" role="alert">
        {{ $message }}
      </div>
    @enderror

    @php
        $remainingAmount = 0;
        if ($payerId) {
            $remainingAmount = $group->contribution_amount - \App\Models\Payment::where('group_id', $group->id)
                ->where('member_id', $memberToPay->id)
                ->where('payer_id', $payerId)
                ->sum('amount');
        }
    @endphp

    <!-- Payer Dropdown -->
    <div class="mb-5">
      <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
        Select Payer
      </label>

      <select wire:model.live="payerId"
          class="select2 w-full border rounded-xl p-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition">
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
              {{ $member->user?->name ?? '-' }}
              (Paid: {{ number_format($alreadyPaidByThisPayer, 2) }} / Remaining: {{ number_format($remainingForThisMember, 2) }})
            </option>
          @endif
      @endforeach
      </select>
      @error('payerId')
        <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
      @enderror
    </div>

    <!-- Amount Input -->
    <div class="mb-6">
      <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
        Amount (Tsh)
      </label>

      <div x-data="{
          display: '',
          raw: null,
          remainingAmt: {{ $remainingAmount ?? 0 }},
          init() {
            this.updateFromRemaining();
          },
          updateFromRemaining() {
            this.remainingAmt = {{ $remainingAmount ?? 0 }};
            if (this.remainingAmt > 0) {
              this.raw = this.remainingAmt;
              this.display = this.formatNumber(this.raw);
              this.$wire.set('paymentAmount', this.raw);
            } else {
              this.raw = null;
              this.display = '';
              this.$wire.set('paymentAmount', null);
            }
          },
          formatNumber(n) {
            try { return Number(n).toLocaleString('en-US', { maximumFractionDigits: 2 }); } catch(e) { return ''; }
          },
          onInput(e) {
            let v = e.target.value;
            v = v.replace(/[^\d.]/g, '');
            const parts = v.split('.');
            if (parts.length > 2) { v = parts[0] + '.' + parts.slice(1).join(''); }
            if (v.includes('.')) {
              const [i, d] = v.split('.');
              v = i + '.' + d.slice(0, 2);
            }
            const n = v === '' || v === '.' ? null : Number(v);
            this.raw = isNaN(n) ? null : n;
            this.$wire.set('paymentAmount', this.raw);
            this.display = v.endsWith('.') ? v : (this.raw === null ? '' : this.formatNumber(this.raw));
          }
        }" 
        class="relative" 
        x-init="$watch('remainingAmt', () => updateFromRemaining())"
        @payerid-changed.window="updateFromRemaining()">
        <input type="text"
             x-model="display"
             x-on:input="onInput($event)"
             inputmode="decimal"
             placeholder="Enter amount"
             class="w-full border rounded-xl p-3 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition"
             @if(($remainingAmount ?? 0) <= 0) disabled @endif>
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
          @if($payerId)
            Remaining: {{ number_format($remainingAmount ?? 0, 2) }}
          @else
            Select a payer to see remaining amount
          @endif
        </div>
        <template x-if="raw !== null && raw > {{ $remainingAmount ?? 0 }}">
          <div class="mt-1 text-xs text-red-600 dark:text-red-400">
            Kiasi kimezidi kilichobaki. Tafadhali punguza.
          </div>
        </template>
        @error('paymentAmount')
          <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-3 pt-4">
      <button wire:click="closePaymentModal"
          class="px-4 py-2 rounded-xl bg-gray-500 dark:bg-gray-700 text-white hover:bg-gray-600 dark:hover:bg-gray-600 transition">
        Cancel
      </button>

      <button wire:click="processPayment"
        wire:loading.attr="disabled"
        wire:target="processPayment"
        class="px-4 py-2 rounded-xl bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
        @if(($remainingAmount ?? 0) <= 0 || empty($payerId) || empty($paymentAmount) || ($paymentAmount <= 0) || (($paymentAmount ?? 0) > ($remainingAmount ?? 0))) disabled @endif>
        
        <span wire:loading.remove wire:target="processPayment">Pay</span>
        
        <span wire:loading wire:target="processPayment" class="inline-flex items-center gap-2">
          <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Processing...
        </span>
      </button>
    </div>

  </div>
</div>
@endif

<!-- Payments List Modal -->
@if($showPaymentsModal)
@php $payments = $this->paymentsSummary; @endphp
@php $total = $this->paymentsTotal; @endphp
<div class="fixed inset-0 z-50 flex items-center justify-center w-full bg-black/50 backdrop-blur-sm p-4"
     @click.self="wire.call('closePaymentsModal')"
     @keydown.escape.window="wire.call('closePaymentsModal')">
  <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl p-6 border border-gray-200 dark:border-gray-700">
    <button wire:click="closePaymentsModal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <div class="mb-4">
      <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payments List</h3>
      <div class="text-sm text-gray-600 dark:text-gray-300">Group: {{ $group->name ?? 'Group' }} • Generated: {{ now()->format('d M, Y H:i') }}</div>
      <div class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">Total: {{ number_format($total, 2) }} Tsh</div>
      <div class="mt-2">
        <a href="{{ route('payments.pdf', $group->id) }}"
           target="_blank"
           class="px-3 py-2 rounded-md text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 inline-block">
          Download PDF
        </a>
      </div>
    </div>

    @if(empty($payments))
      <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">No payments yet.</div>
    @else
      <div x-ref="paymentsContent">
        <div class="w-full overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
              <th class="px-3 py-2">Mlipaji</th>
              <th class="px-3 py-2">Mpokeaji</th>
              <th class="px-3 py-2">Kiasi (Tsh)</th>
              <th class="px-3 py-2">Tarehe ya Malipo</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($payments as $p)
              <tr class="text-gray-900 dark:text-gray-100">
                <td class="px-3 py-2">{{ $p['payer'] }}</td>
                <td class="px-3 py-2">{{ $p['receiver'] }}</td>
                <td class="px-3 py-2 font-semibold">{{ number_format($p['amount'], 2) }}</td>
                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $p['date'] }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td class="px-3 py-2" colspan="2">Total</td>
              <td class="px-3 py-2 font-semibold">{{ number_format($total, 2) }}</td>
              <td class="px-3 py-2"></td>
            </tr>
          </tfoot>
        </table>
        </div>
      </div>
    @endif
  </div>
</div>
@endif
   
    <div class="w-full text-center">
      @if(auth()->id() === $this->group->owner_id)
      <button type="button" wire:click="saveOrder" class="rounded-md bg-cyan-600 text-white px-6 py-2 text-sm font-semibold shadow hover:bg-cyan-700 transition">Save Order</button>
      @else
      <div class="text-sm text-gray-500 dark:text-gray-400">Ni mmiliki wa kikundi tu anayeweza kuhifadhi mpangilio.</div>
      @endif
    </div>
  </div>
  

</section>
      </div>

















 