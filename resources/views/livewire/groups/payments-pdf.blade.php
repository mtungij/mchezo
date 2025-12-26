<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payments - {{ $group->name }}</title>
  <style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
        border-radius: 6px;
        overflow: hidden;
    }

    thead th {
        background: linear-gradient(135deg, #06b6d4, #0891b2); /* cyan */
        color: #ffffff;
        font-weight: 600;
        padding: 12px;
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #0e7490;
    }

    td {
        padding: 10px 12px;
        font-size: 13px;
        border-bottom: 1px solid #e5e7eb;
        color: #1f2937;
    }

    tbody tr:nth-child(even) {
        background-color: #f0fdfa; /* soft cyan tint */
    }

    tbody tr:hover {
        background-color: #cffafe;
    }

    .amount {
        text-align: right;
        font-weight: 600;
        color: #0e7490;
    }

    .payer-name {
        font-weight: 500;
    }

    .section-total {
        background-color: #ecfeff;
        font-weight: 700;
        color: #0e7490;
    }
</style>

</head>
<body>
    <div class="header">
        <h1> Payment Report</h1>
        <div class="header-meta">
            <span>Group: <strong>{{ $group->name }}</strong></span>
            <span>Generated: {{ now()->format('d M, Y H:i') }}</span>
        </div>
    </div>

    <div class="summary">
        <div class="summary-card">
            <div class="summary-label">Total Members</div>
            <div class="summary-value">{{ $payments->groupBy('member_id')->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Amount</div>
            <div class="summary-value">{{ number_format($total, 0) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Group Size</div>
            <div class="summary-value">{{ $group->groupMembers->count() }}</div>
        </div>
    </div>

    @if($payments->isEmpty())
        <p style="text-align: center; color: #a0aec0;">No payments recorded yet.</p>
    @else
        @php
            // Group payments by receiver
            $groupedByReceiver = collect($paymentRows)->groupBy('receiver');
            // Get member contribution dates
            $memberDates = $group->groupMembers->pluck('id', 'user_id');
        @endphp
        
        @foreach($groupedByReceiver as $receiver => $receiverPayments)
            @php
                $payersCount = $receiverPayments->count();
                $receiverTotal = $receiverPayments->sum('amount');
                // Find receiver member to get contribution date
                $receiverMember = $group->groupMembers->where('user.name', $receiver)->first();
                $startDate = \Carbon\Carbon::parse($group->start_date);
                $interval = $group->interval;
                $contributionDate = $startDate->copy();
                switch ($group->frequency_type) {
                    case 'day':
                        $contributionDate = $startDate->copy()->addDays($interval * ($receiverMember->order_position ?? 0));
                        break;
                    case 'week':
                        $contributionDate = $startDate->copy()->addWeeks($interval * ($receiverMember->order_position ?? 0));
                        break;
                    case 'month':
                        $contributionDate = $startDate->copy()->addMonths($interval * ($receiverMember->order_position ?? 0));
                        break;
                }
            @endphp
            
            <div class="receiver-section">
                <div class="receiver-header">
                    <div class="receiver-name">{{ $receiver }}</div>
                    <div class="receiver-details">
                        <span class="detail-badge">📅 Contribution: {{ $contributionDate->format('d M, Y') }}</span>
                       
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Payer</th>
                            <th>Amount (Tsh)</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receiverPayments as $p)
                            <tr>
                                <td class="payer-name">{{ $p['payer'] }}</td>
                                <td class="amount">{{ number_format($p['amount'], 2) }}</td>
                                <td>{{ $p['date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="section-total">
                            <td>Subtotal</td>
                            <td class="amount">{{ number_format($receiverTotal, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    @endif

    <div class="grand-total">
        Grand Total: {{ number_format($total, 2) }} Tsh
    </div>
    
    <div class="footer">
        <p>Taarifa hii imetumwa na mfumo. Wasiliana na msimamizi wa kikundi kwa msaada.</p>
    </div>
</body>
</html>
