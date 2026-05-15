<?php

namespace App\Services;

use App\Models\ThanhToan;
use Carbon\Carbon;

class SepayService
{
    protected string $bankCode;
    protected string $accountNumber;
    protected string $accountName;
    protected string $apiKey;
    protected string $qrTemplate;
    protected int $expiryMinutes;

    public function __construct()
    {
        $this->bankCode = config('sepay.bank_code');
        $this->accountNumber = config('sepay.account_number');
        $this->accountName = config('sepay.account_name');
        $this->apiKey = config('sepay.api_key');
        $this->qrTemplate = config('sepay.qr_template', 'compact2');
        $this->expiryMinutes = config('sepay.payment_expiry_minutes', 15);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey)
            && !empty($this->accountNumber)
            && !empty($this->bankCode);
    }

    public function generateQrUrl(float $amount, string $content): string
    {
        $params = http_build_query([
            'amount' => (int) $amount,
            'addInfo' => $content,
            'accountName' => $this->accountName,
        ]);

        return "https://img.vietqr.io/image/{$this->bankCode}-{$this->accountNumber}-{$this->qrTemplate}.png?{$params}";
    }

    public function getPaymentInfo(ThanhToan $thanhToan): array
    {
        return [
            'bank_code' => $this->bankCode,
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName,
            'amount' => $thanhToan->tong_tien_sau_giam,
            'content' => $thanhToan->ma_thanh_toan,
            'qr_url' => $this->generateQrUrl(
                $thanhToan->tong_tien_sau_giam,
                $thanhToan->ma_thanh_toan
            ),
            'expires_at' => $thanhToan->het_han_luc?->toIso8601String(),
            'expiry_minutes' => $this->expiryMinutes,
        ];
    }

    public function verifyWebhook(?string $apiKey): bool
    {
        if (empty($this->apiKey)) {
            return false;
        }

        return $apiKey === $this->apiKey;
    }

    public function parseTransaction(array $payload): ?array
    {
        $content = $payload['content'] ?? $payload['description'] ?? '';
        $amount = (float) ($payload['transferAmount'] ?? $payload['amount'] ?? 0);
        $transactionId = $payload['referenceCode'] ?? $payload['id'] ?? null;

        if (empty($content) || $amount <= 0) {
            return null;
        }

        return [
            'content' => $content,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'transfer_type' => $payload['transferType'] ?? 'in',
            'gateway' => $payload['gateway'] ?? '',
            'transaction_date' => $payload['transactionDate'] ?? now()->toDateTimeString(),
        ];
    }

    public function matchTransaction(array $parsed): ?ThanhToan
    {
        $content = strtoupper(trim($parsed['content']));

        return ThanhToan::where('trang_thai', 'cho_thanh_toan')
            ->where('hinh_thuc_thanh_toan', 'chuyen_khoan')
            ->whereRaw('UPPER(ma_thanh_toan) != ""')
            ->get()
            ->first(function ($thanhToan) use ($content) {
                return str_contains($content, strtoupper($thanhToan->ma_thanh_toan));
            });
    }

    public function getExpiryMinutes(): int
    {
        return $this->expiryMinutes;
    }
}
