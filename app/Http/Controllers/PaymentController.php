<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function downloadPdf(Group $group)
    {
        // Verify user has access to this group
        if (auth()->id() !== $group->owner_id && !$group->groupMembers()->where('user_id', auth()->id())->exists()) {
            abort(403, 'Unauthorized');
        }

        // Fetch payments
        $payments = Payment::where('group_id', $group->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Build payment rows with names and dates
        $paymentRows = [];
        $total = 0;

        foreach ($payments as $p) {
            $payerMember = GroupMember::find($p->payer_id);
            $receiverMember = GroupMember::find($p->member_id);

            $paymentRows[] = [
                'payer' => $payerMember?->user?->name ?? '-',
                'receiver' => $receiverMember?->user?->name ?? '-',
                'amount' => $p->amount,
                'date' => optional($p->created_at)->format('d M, Y H:i'),
            ];

            $total += $p->amount;
        }

        // Generate PDF
        $pdf = Pdf::loadView('livewire.groups.payments-pdf', [
            'group' => $group,
            'payments' => $payments,
            'paymentRows' => $paymentRows,
            'total' => $total,
        ]);

        return $pdf->download('payments-' . $group->id . '-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
