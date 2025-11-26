<div class="dark:bg-gray-900 dark:text-gray-100 min-h-screen p-4 transition duration-300">
    <div class="p-6 max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg mt-10 transition">

        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
            Arrange Members for Payout: {{ $group->name }}
        </h2>

        @if(session()->has('success'))
            <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($group->groupMembers->isEmpty())
            <p class="text-gray-700 dark:text-gray-300">No members have joined yet.</p>
        @else
            <form wire:submit.prevent="saveOrder">
     <table class="w-full text-left border-collapse mt-4">
    <thead>
        <tr class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <th class="p-2 border dark:border-gray-600">S/No</th>
            <th class="p-2 border dark:border-gray-600">Name</th>
            <th class="p-2 border dark:border-gray-600">Phone</th>
            <th class="p-2 border dark:border-gray-600">Login Code</th>
            <th class="p-2 border dark:border-gray-600">Order Position</th>
            <th class="p-2 border dark:border-gray-600">Amount Due (Tsh)</th>
            <th class="p-2 border dark:border-gray-600">Amount Paid (Tsh)</th>
            <th class="p-2 border dark:border-gray-600">Status</th>
            <th class="p-2 border dark:border-gray-600">Payout Date</th>
            <th class="p-2 border dark:border-gray-600">Pay</th>
        </tr>
    </thead>

    <tbody>
        @foreach($this->getPayoutSchedule() as $member)
            <tr class="border-b border-gray-300 dark:border-gray-700">
                <td class="p-2 border dark:border-gray-700 text-center">{{ $member['order_position'] }}</td>
                <td class="p-2 border dark:border-gray-700">{{ $member['name'] }}</td>
                <td class="p-2 border dark:border-gray-700">{{ $member['phone'] }}</td>
                <td class="p-2 border dark:border-gray-700">{{ $member['login_code'] }}</td>

                <!-- ORDER INPUT -->
                <td class="p-2 border dark:border-gray-700 text-center">
                    <input type="number"
                           min="1"
                           max="{{ $group->groupMembers->count() }}"
                           wire:model.defer="membersOrder.{{ $member['id'] }}"
                           class="w-16 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded p-1 text-center">
                </td>

                <td class="p-2 border dark:border-gray-700 text-center">
                    {{ number_format($member['amount_due'], 2) }}
                </td>

                <td class="p-2 border dark:border-gray-700 text-center">
                    {{ number_format($member['amount_paid'], 2) }}
                </td>

                <td class="p-2 border dark:border-gray-700 text-center">
                    @if($member['is_paid'])
                        <span class="text-green-600 font-semibold">Paid</span>
                    @else
                        <span class="text-red-600 font-semibold">Not Paid</span>
                    @endif
                </td>

                <td class="p-2 border dark:border-gray-700 text-center">
                    {{ $member['pay_date'] }}
                </td>

                <!-- PAY BUTTON -->
                <td class="p-2 border dark:border-gray-700 text-center">
                    <button type="button"
                            wire:click="openPaymentModal({{ $member['id'] }})"
                            class="bg-green-600 text-white px-2 py-1 rounded"
                            @if($memberToPay->id != $member['id']) disabled @endif>
                        Pay Member
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


@if($showPaymentModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-96 transition">

            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Pay to: {{ $memberToPay->user->name ?? '-' }}
            </h3>

            @php
                // Jumla ya malipo yote yanayohitajika kwa member huyu
                $totalRequired = $group->contribution_amount * $group->groupMembers->count();
                // Malipo yote yaliyofanywa tayari kwa member huyu
                $alreadyPaid = \App\Models\Payment::where('group_id', $group->id)
                                    ->where('member_id', $memberToPay->id)
                                    ->sum('amount');
                // C kiasi kinachobaki kulipwa
                $remainingAmount = max(0, $totalRequired - $alreadyPaid);
            @endphp

            <!-- Select Payer -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200 mb-1">Select Payer</label>
                <select wire:model="payerId"
                        class="w-full border rounded p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">-- Select Member --</option>
                    @foreach($group->groupMembers as $member)
                        @php
                            // Kiasi alicholipa huyu payer kwa memberToPay
                            $alreadyPaidByThisPayer = \App\Models\Payment::where('group_id', $group->id)
                                                        ->where('member_id', $memberToPay->id)
                                                        ->where('payer_id', $member->id)
                                                        ->sum('amount');
                        @endphp
                        @if($member->id != $memberToPay->id && $alreadyPaidByThisPayer < $group->contribution_amount)
                            <option value="{{ $member->id }}">
                                {{ $member->user->name ?? '-' }}
                                (Already Paid: {{ number_format($alreadyPaidByThisPayer,2) }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Amount -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-200 mb-1">Amount (Tsh)</label>
                <input type="number"
                       wire:model="paymentAmount"
                       class="w-full border rounded p-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                       max="{{ $remainingAmount }}"
                       placeholder="Enter amount to pay"
                       @if($remainingAmount <= 0) disabled @endif>
                <small class="text-gray-500 dark:text-gray-400">
                    Remaining: {{ number_format($remainingAmount,2) }} Tsh
                </small>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2">
                <button wire:click="closePaymentModal"
                        class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-500 transition">
                    Cancel
                </button>

                <button wire:click="processPayment"
                        class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition"
                        @if($remainingAmount <= 0) disabled @endif>
                    Pay
                </button>
            </div>
        </div>
    </div>
@endif


                <!-- SAVE BUTTON -->
                <button type="submit"
                        class="mt-4 bg-blue-600 dark:bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                    Save Order
                </button>
            </form>
        @endif
    </div>
</div>
