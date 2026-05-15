<?php

namespace App\Console\Commands;

use App\Models\ThanhToan;
use Illuminate\Console\Command;

class ExpirePayments extends Command
{
    protected $signature = 'payments:expire';
    protected $description = 'Chuyển giao dịch chuyển khoản quá hạn sang trạng thái hết hạn';

    public function handle(): int
    {
        $expired = ThanhToan::where('trang_thai', 'cho_thanh_toan')
            ->where('hinh_thuc_thanh_toan', 'chuyen_khoan')
            ->where('het_han_luc', '<', now())
            ->update(['trang_thai' => 'het_han']);

        if ($expired > 0) {
            $this->info("Đã chuyển {$expired} giao dịch sang trạng thái hết hạn.");
        }

        return self::SUCCESS;
    }
}
