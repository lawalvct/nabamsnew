<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_AWAITING = 'awaiting_payment';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_AWAITING,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    public const METHODS = [
        'transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'pos' => 'POS',
        'other' => 'Other',
    ];

    // Capital letters and digits without look-alikes (0/O, 1/I).
    private const REFERENCE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const REFERENCE_LENGTH = 8;

    protected $fillable = [
        'reference',
        'user_id',
        'academic_session_id',
        'semester',
        'level_id',
        'level_name',
        'items',
        'amount_due',
        'amount_paid',
        'bank_account_id',
        'bank_snapshot',
        'payment_method',
        'payer_name',
        'paid_at',
        'evidence_path',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'reviewer_name',
        'rejection_reason',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'bank_snapshot' => 'array',
            'amount_due' => 'integer',
            'amount_paid' => 'integer',
            'paid_at' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            $payment->reference ??= self::generateReference();
        });
    }

    public static function generateReference(): string
    {
        $maxIndex = strlen(self::REFERENCE_ALPHABET) - 1;

        do {
            $reference = '';

            for ($i = 0; $i < self::REFERENCE_LENGTH; $i++) {
                $reference .= self::REFERENCE_ALPHABET[random_int(0, $maxIndex)];
            }
        } while (self::query()->where('reference', $reference)->exists());

        return $reference;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canBeSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_AWAITING, self::STATUS_REJECTED], true);
    }

    public function periodLabel(): string
    {
        $session = $this->academicSession?->name ?? 'Session';

        return $this->semester ? "{$session}, {$this->semester} Semester" : "{$session} (Full Session)";
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_AWAITING => 'Not paid',
            self::STATUS_PENDING => 'Awaiting verification',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusClasses(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'bg-[#1FA774]/10 text-[#1FA774]',
            self::STATUS_PENDING => 'bg-[#0A2A6B]/10 text-[#0A2A6B]',
            self::STATUS_REJECTED => 'bg-red-50 text-red-700',
            default => 'bg-[#F5B400]/20 text-[#0A2A6B]',
        };
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->payment_method] ?? 'Bank Transfer';
    }

    public function paidToLabel(): string
    {
        if (! $this->bank_snapshot) {
            return $this->methodLabel();
        }

        return trim(($this->bank_snapshot['bank_name'] ?? '').' · '.($this->bank_snapshot['account_number'] ?? '').' ('.($this->bank_snapshot['account_name'] ?? '').')');
    }

    public function evidenceIsImage(): bool
    {
        return (bool) preg_match('/\.(jpe?g|png|webp)$/i', (string) $this->evidence_path);
    }
}
