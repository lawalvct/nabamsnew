<?php

namespace App\Support;

use App\Models\AcademicSession;
use App\Models\AppSetting;
use App\Models\Level;
use App\Models\Payment;
use App\Models\PriceSetting;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Works out what a member owes for the current period and whether they may use the dashboard.
 *
 * Rules (set by admin in Settings):
 *  - off:              nobody is locked.
 *  - session:          one payment per session covering the prices of every semester.
 *  - session_semester: one payment per semester of the current session.
 *
 * A member is never locked when there is no current session or no active price for their level.
 */
class PaymentRequirement
{
    private function __construct(
        public readonly string $mode,
        public readonly ?AcademicSession $session,
        public readonly ?string $semester,
        public readonly Collection $items,
        public readonly ?Payment $payment,
        public readonly bool $satisfied,
    ) {}

    public static function for(User $user): self
    {
        $mode = AppSetting::paymentRequirementMode();

        if (strtolower((string) $user->role) === 'admin' || $mode === AppSetting::PAYMENT_OFF) {
            return new self($mode, null, null, collect(), null, true);
        }

        $session = AcademicSession::current()->first();

        if (! $session) {
            return new self($mode, null, null, collect(), null, true);
        }

        $semester = $mode === AppSetting::PAYMENT_SESSION_SEMESTER
            ? ($session->current_semester ?: 'First')
            : null;

        $items = self::priceItems($user->level_id, $session->id, $semester);

        $payment = Payment::query()
            ->where('user_id', $user->id)
            ->where('academic_session_id', $session->id)
            ->where(fn ($query) => $semester ? $query->where('semester', $semester) : $query->whereNull('semester'))
            ->latest('id')
            ->first();

        $satisfied = $items->isEmpty() || self::hasApprovedCoverage($user, $session, $semester, $items);

        return new self($mode, $session, $semester, $items, $payment, $satisfied);
    }

    /**
     * Active prices for a level in a session; a null semester means every semester (full session).
     *
     * @return Collection<int, array{name: string, semester: string, amount: int}>
     */
    public static function priceItems(?int $levelId, int $sessionId, ?string $semester): Collection
    {
        return PriceSetting::active()
            ->where('academic_session_id', $sessionId)
            ->where('level_id', $levelId)
            ->when($semester, fn ($query) => $query->where('semester', $semester))
            ->orderBy('semester')
            ->orderBy('name')
            ->get()
            ->map(fn (PriceSetting $price) => [
                'name' => $price->name,
                'semester' => $price->semester,
                'amount' => (int) $price->amount,
            ])
            ->values();
    }

    public function amount(): int
    {
        return (int) $this->items->sum('amount');
    }

    public function isPending(): bool
    {
        return $this->payment?->status === Payment::STATUS_PENDING;
    }

    public function periodLabel(): string
    {
        if (! $this->session) {
            return '';
        }

        return $this->semester
            ? "{$this->session->name}, {$this->semester} Semester"
            : "{$this->session->name} (Full Session)";
    }

    /**
     * Get the open payment for this period, creating one (with its reference) when needed.
     * While a payment is still unpaid, its items are refreshed so price edits are picked up.
     */
    public function openPayment(User $user): ?Payment
    {
        if ($this->satisfied || ! $this->session) {
            return $this->payment;
        }

        $payment = $this->payment;

        if ($payment && $payment->status !== Payment::STATUS_AWAITING) {
            return $payment;
        }

        $attributes = [
            'level_id' => $user->level_id,
            'level_name' => Level::query()->whereKey($user->level_id)->value('name') ?? $user->academic_level,
            'items' => $this->items->all(),
            'amount_due' => $this->amount(),
        ];

        if ($payment) {
            $payment->update($attributes);

            return $payment;
        }

        return Payment::create([
            ...$attributes,
            'user_id' => $user->id,
            'academic_session_id' => $this->session->id,
            'semester' => $this->semester,
            'status' => Payment::STATUS_AWAITING,
        ]);
    }

    private static function hasApprovedCoverage(User $user, AcademicSession $session, ?string $semester, Collection $items): bool
    {
        $approved = Payment::approved()
            ->where('user_id', $user->id)
            ->where('academic_session_id', $session->id)
            ->get(['semester']);

        // A full-session payment always covers the session and each of its semesters.
        if ($approved->contains(fn (Payment $payment) => $payment->semester === null)) {
            return true;
        }

        if ($semester) {
            return $approved->contains('semester', $semester);
        }

        // Session mode: separate semester payments count when every priced semester is paid.
        return $items->pluck('semester')
            ->unique()
            ->every(fn (string $pricedSemester) => $approved->contains('semester', $pricedSemester));
    }
}
