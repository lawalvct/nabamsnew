<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptVerificationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $reference = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->query('reference')));
        $payment = null;

        if (strlen($reference) === 8) {
            $payment = Payment::approved()
                ->with(['user', 'academicSession'])
                ->where('reference', $reference)
                ->first();
        }

        return view('receipts.verify', [
            'reference' => $reference,
            'searched' => $reference !== '',
            'payment' => $payment,
        ]);
    }

    public static function maskMatno(?string $matno): string
    {
        if (! $matno) {
            return '-';
        }

        $visible = max(0, strlen($matno) - 4);

        return substr($matno, 0, $visible).str_repeat('*', strlen($matno) - $visible);
    }
}
