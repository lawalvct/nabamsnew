<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>NABAMS Receipt {{ $payment->reference }}</title>
        <style>
            * { box-sizing: border-box; }
            body { color: #2E2E2E; font-family: DejaVu Sans, sans-serif; font-size: 10.5px; line-height: 1.4; margin: 0; }
            .header { background: #0A2A6B; color: #FFFFFF; padding: 14px 28px; }
            .header table { width: 100%; }
            .header h1 { font-size: 22px; margin: 0; }
            .header p { margin: 2px 0 0; color: #D8DDE8; }
            .brand { color: #F5B400; font-size: 10px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
            .logo { background: #FFFFFF; border-radius: 6px; height: 48px; padding: 4px; width: 48px; }
            .content { padding: 16px 28px; }
            .stamp { border: 2px solid #1FA774; border-radius: 6px; color: #1FA774; display: inline-block; font-size: 14px; font-weight: bold; letter-spacing: 2px; padding: 4px 12px; }
            .reference { background: #F2F2F2; border-radius: 6px; margin: 0 0 8px; padding: 10px 16px; }
            .reference .code { color: #0A2A6B; font-family: DejaVu Sans Mono, monospace; font-size: 22px; font-weight: bold; letter-spacing: 4px; }
            h2 { color: #0A2A6B; font-size: 12px; margin: 14px 0 4px; text-transform: uppercase; }
            table.grid { border-collapse: collapse; width: 100%; }
            table.grid td, table.grid th { border-bottom: 1px solid #D8DDE8; padding: 4px 8px; text-align: left; vertical-align: top; }
            table.grid th { background: #F2F2F2; color: #0A2A6B; font-size: 10px; text-transform: uppercase; }
            .label { color: #6B7280; width: 32%; }
            .strong { color: #0A2A6B; font-weight: bold; }
            .right { text-align: right !important; }
            .total td { border-top: 2px solid #0A2A6B; border-bottom: none; font-size: 13px; }
            .footer { border-top: 1px solid #D8DDE8; color: #6B7280; font-size: 9px; margin-top: 16px; padding-top: 8px; }
        </style>
    </head>
    <body>
        @php
            $member = $payment->user;
            $logo = public_path('logo.png');
        @endphp

        <div class="header">
            <table>
                <tr>
                    <td style="width: 64px;">
                        @if (file_exists($logo))
                            <img src="{{ $logo }}" class="logo" alt="NABAMS">
                        @endif
                    </td>
                    <td>
                        <p class="brand">National Association of Business Administration &amp; Management Students</p>
                        <h1>Payment Receipt</h1>
                        <p>{{ $payment->periodLabel() }}</p>
                    </td>
                    <td class="right" style="text-align: right;">
                        <span class="stamp">PAID</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <div class="reference">
                <span class="label">Receipt Reference</span><br>
                <span class="code">{{ $payment->reference }}</span>
            </div>

            <h2>Member</h2>
            <table class="grid">
                <tr><td class="label">Name</td><td class="strong">{{ $member?->name }}</td></tr>
                <tr><td class="label">Matric Number</td><td>{{ $member?->matno ?: '-' }}</td></tr>
                <tr><td class="label">Level</td><td>{{ $payment->level_name ?: $member?->academic_level }}</td></tr>
                <tr><td class="label">Email</td><td>{{ $member?->email }}</td></tr>
            </table>

            <h2>Payment Details</h2>
            <table class="grid">
                <thead>
                    <tr><th>Item</th><th class="right">Amount</th></tr>
                </thead>
                <tbody>
                    @foreach ($payment->items ?? [] as $item)
                        <tr>
                            <td>{{ $item['name'] }}@if (! empty($item['semester']) && ! $payment->semester) ({{ $item['semester'] }} Semester)@endif</td>
                            <td class="right">NGN {{ number_format($item['amount']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td class="strong">Amount Due</td>
                        <td class="right strong">NGN {{ number_format($payment->amount_due) }}</td>
                    </tr>
                    <tr class="total">
                        <td class="strong">Amount Paid</td>
                        <td class="right strong">NGN {{ number_format($payment->amount_paid) }}</td>
                    </tr>
                </tbody>
            </table>

            <h2>Transaction</h2>
            <table class="grid">
                <tr><td class="label">Payment Method</td><td>{{ $payment->methodLabel() }}</td></tr>
                @if ($payment->bank_snapshot)
                    <tr><td class="label">Paid Into</td><td>{{ $payment->paidToLabel() }}</td></tr>
                @endif
                <tr><td class="label">Payer Name</td><td>{{ $payment->payer_name }}</td></tr>
                <tr><td class="label">Date of Payment</td><td>{{ $payment->paid_at?->format('F j, Y') }}</td></tr>
                <tr><td class="label">Submitted</td><td>{{ $payment->submitted_at?->format('F j, Y g:i A') }}</td></tr>
                <tr><td class="label">Approved By</td><td class="strong">{{ $payment->reviewer_name }}</td></tr>
                <tr><td class="label">Approved On</td><td>{{ $payment->reviewed_at?->format('F j, Y g:i A') }}</td></tr>
            </table>

            <div class="footer">
                This receipt was generated by the NABAMS portal on {{ now()->format('F j, Y g:i A') }}. Quote reference {{ $payment->reference }} for any enquiry about this payment.
                Verify this receipt at {{ route('receipts.verify', ['reference' => $payment->reference]) }}
            </div>
        </div>
    </body>
</html>
