<?php

namespace App\Http\Middleware;

use App\Support\PaymentRequirement;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaymentVerified
{
    /**
     * Routes a member can still use while their payment for the current period is outstanding.
     */
    private const ALLOWED_ROUTES = [
        'payments.*',
        'profile.*',
        'logout',
        'admin.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $requirement = PaymentRequirement::for($user);
        $request->attributes->set('paymentRequirement', $requirement);

        if ($requirement->session) {
            $feePaid = $requirement->satisfied ? 'Yes' : 'No';

            if ($user->fee_paid !== $feePaid) {
                $user->forceFill(['fee_paid' => $feePaid])->saveQuietly();
            }
        }

        if ($requirement->satisfied || $request->routeIs(...self::ALLOWED_ROUTES)) {
            return $next($request);
        }

        $message = $requirement->isPending()
            ? 'Your payment for '.$requirement->periodLabel().' is awaiting verification. Your dashboard will open once it is approved.'
            : 'Please pay your dues for '.$requirement->periodLabel().' to continue using the dashboard.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 402);
        }

        return redirect()->route('payments.create')->with('payment_notice', $message);
    }
}
