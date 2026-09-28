<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public readonly Payment $payment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payment = $this->payment;
        $amount = '₦'.number_format((int) ($payment->amount_paid ?? $payment->amount_due));
        $mail = (new MailMessage)->greeting('Hello '.($notifiable->firstname ?: 'NABAMS Member').',');

        return match ($payment->status) {
            Payment::STATUS_APPROVED => $payment->isResourcePurchase()
                ? $mail
                    ->subject("Purchase approved - {$payment->reference}")
                    ->line("Your payment of {$amount} for {$payment->periodLabel()} has been verified and approved.")
                    ->line('You can now download it any time from the Resources page.')
                    ->action('Open Resource', $payment->resource ? route('resources.show', $payment->resource) : route('resources.index'))
                : $mail
                ->subject("Payment approved - {$payment->reference}")
                ->line("Your payment of {$amount} for {$payment->periodLabel()} has been verified and approved.")
                ->line('Your NABAMS dashboard is now fully open. You can download your receipt from the Transactions page.')
                ->action('Download Receipt', route('payments.receipt', $payment)),
            Payment::STATUS_REJECTED => $mail
                ->subject("Payment could not be verified - {$payment->reference}")
                ->line("We could not verify your payment for {$payment->periodLabel()}.")
                ->line('Reason: '.($payment->rejection_reason ?: 'Not specified'))
                ->line('Please check the details and upload the correct evidence using the same reference.')
                ->action('Update Payment', $payment->isResourcePurchase() ? route('payments.show', $payment) : route('payments.create')),
            default => $mail
                ->subject("Payment received - {$payment->reference}")
                ->line("We have received your payment evidence of {$amount} for {$payment->periodLabel()}.")
                ->line($payment->isResourcePurchase()
                    ? 'An admin will verify it within 24 hours. You can download the resource once it is approved.'
                    : 'An admin will verify it within 24 hours. Your dashboard will open once it is approved.')
                ->action('View Transactions', route('payments.index')),
        };
    }

    /**
     * Send without letting a mail failure break the payment action.
     */
    public static function sendTo(Payment $payment): void
    {
        try {
            $payment->user?->notify(new self($payment));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
