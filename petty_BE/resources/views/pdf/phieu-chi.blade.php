<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phiếu chi - {{ $phieuChi->ma_phieu_chi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #101828;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 30px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }

        .header h1 {
            font-size: 24px;
            color: #101828;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .header .subtitle {
            font-size: 13px;
            color: #717182;
        }

        /* Info Section */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 8px 10px;
            font-weight: 600;
            color: #4a5565;
            width: 35%;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 8px 10px;
            color: #101828;
            vertical-align: top;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid;
        }

        .badge-purchase {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1447e6;
        }

        .badge-operating {
            background: #fef9c2;
            border-color: #eab308;
            color: #a65f00;
        }

        .badge-completed {
            background: #dcfce7;
            border-color: #16a34a;
            color: #008236;
        }

        .badge-debt {
            background: #ffedd4;
            border-color: #f97316;
            color: #ca3500;
        }

        /* Summary Section */
        .summary-box {
            background: #fef2f2;
            border: 2px solid #ffc9c9;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            width: 33.33%;
        }

        .summary-label {
            font-size: 12px;
            color: #4a5565;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            margin-top: 5px;
        }

        .text-total {
            color: #101828;
        }

        .text-paid {
            color: #00a63e;
        }

        .text-remaining {
            color: #e7000b;
        }

        /* Payment History */
        .history-section {
            margin-top: 25px;
        }

        .history-header {
            font-size: 16px;
            font-weight: 700;
            color: #101828;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .history-item {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .history-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .history-left {
            display: table-cell;
            width: 70%;
        }

        .history-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .payment-number {
            display: inline-block;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 10px;
        }

        .payment-date {
            color: #4a5565;
            font-size: 12px;
        }

        .payment-method {
            color: #101828;
            font-size: 13px;
            margin: 8px 0;
        }

        .payment-details {
            background: #f9fafb;
            border-left: 3px solid #e5e7eb;
            padding: 8px 12px;
            margin: 8px 0;
            font-size: 11px;
        }

        .payment-details-item {
            margin: 3px 0;
            color: #4a5565;
        }

        .payment-amount {
            font-size: 15px;
            margin: 8px 0;
        }

        .payment-amount strong {
            color: #00a63e;
            font-weight: 700;
        }

        .payment-note {
            color: #4a5565;
            font-size: 12px;
            font-style: italic;
            margin-top: 5px;
        }

        .badge-success {
            background: #dcfce7;
            border-color: #16a34a;
            color: #008236;
        }

        .empty-state {
            background: #f9fafb;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #717182;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 11px;
            color: #717182;
        }

        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Chi tiết phiếu chi - {{ $phieuChi->ma_phieu_chi }}</h1>
            <p class="subtitle">Thông tin chi tiết và lịch sử thanh toán</p>
        </div>

        <!-- Info Section -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Loại chi:</div>
                <div class="info-value">
                    <span
                        class="badge {{ $phieuChi->loai_phieu_chi === 'chi_nhap_hang' ? 'badge-purchase' : 'badge-operating' }}">
                        {{ $phieuChi->loai_phieu_chi_label }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Đối tượng nhận tiền:</div>
                <div class="info-value">
                    @if ($phieuChi->loai_phieu_chi === 'chi_nhap_hang' && $phieuChi->nhaCungCap)
                        {{ $phieuChi->nhaCungCap->ten_nha_cung_cap }}
                    @else
                        {{ $phieuChi->doi_tuong_nhan_tien ?? 'Chưa xác định' }}
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">Nội dung chi:</div>
                <div class="info-value">{{ $phieuChi->ly_do_chi }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Ngày tạo:</div>
                <div class="info-value">{{ $phieuChi->created_at->format('H:i - d/m/Y') }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Người tạo:</div>
                <div class="info-value">{{ $phieuChi->nguoi_tao_info['name'] ?? 'N/A' }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Trạng thái:</div>
                <div class="info-value">
                    <span
                        class="badge {{ $phieuChi->trang_thai === 'da_hoan_thanh' ? 'badge-completed' : 'badge-debt' }}">
                        {{ $phieuChi->trang_thai_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Summary Box -->
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Tổng giá trị</div>
                    <div class="summary-value text-total">{{ number_format($phieuChi->tong_so_tien, 0, ',', '.') }}đ
                    </div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Đã trả</div>
                    <div class="summary-value text-paid">
                        {{ number_format($phieuChi->so_tien_thanh_toan_ngay, 0, ',', '.') }}đ</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Còn nợ</div>
                    <div class="summary-value text-remaining">
                        {{ number_format($phieuChi->so_tien_con_no, 0, ',', '.') }}đ</div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="history-section">
            <div class="history-header">Lịch sử thanh toán</div>

            @php
                $paymentHistory = [];

                // Lấy lịch sử thanh toán từ database
                $lichSuThanhToan = $phieuChi->lichSuThanhToan;

                if ($lichSuThanhToan && $lichSuThanhToan->count() > 0) {
                    foreach ($lichSuThanhToan as $lichSu) {
                        $paymentHistory[] = [
                            'date' => $lichSu->ngay_thanh_toan->format('H:i - d/m/Y'),
                            'method' => $lichSu->hinh_thuc_thanh_toan_label,
                            'amount' => $lichSu->so_tien_thanh_toan,
                            'cashAmount' => $lichSu->tien_mat,
                            'transferAmount' => $lichSu->tien_chuyen_khoan,
                            'note' => $lichSu->ghi_chu ?? 'Không có ghi chú',
                            'nguoi_thanh_toan' => $lichSu->nguoi_thanh_toan_info['name'] ?? 'N/A',
                        ];
                    }
                } else {
                    // Nếu chưa có lịch sử, hiển thị thanh toán ban đầu
                    if ($phieuChi->so_tien_thanh_toan_ngay > 0) {
                        $paymentHistory[] = [
                            'date' => $phieuChi->ngay_chi->format('H:i - d/m/Y'),
                            'method' => 'Thanh toán lần đầu',
                            'amount' => $phieuChi->so_tien_thanh_toan_ngay,
                            'cashAmount' => $phieuChi->tien_mat,
                            'transferAmount' => $phieuChi->tien_chuyen_khoan,
                            'note' => $phieuChi->ghi_chu ?? 'Thanh toán khi tạo phiếu',
                            'nguoi_thanh_toan' => $phieuChi->nguoi_tao_info['name'] ?? 'N/A',
                        ];
                    }
                }
            @endphp

            @if (count($paymentHistory) > 0)
                @foreach ($paymentHistory as $index => $payment)
                    <div class="history-item">
                        <div class="history-top">
                            <div class="history-left">
                                <div>
                                    <span class="payment-number">Lần {{ $index + 1 }}</span>
                                    <span class="payment-date">{{ $payment['date'] }}</span>
                                </div>
                                <div class="payment-method">{{ $payment['method'] }}</div>

                                @if ($payment['cashAmount'] > 0 || $payment['transferAmount'] > 0)
                                    <div class="payment-details">
                                        @if ($payment['cashAmount'] > 0)
                                            <div class="payment-details-item">
                                                💵 Tiền mặt:
                                                <strong>{{ number_format($payment['cashAmount'], 0, ',', '.') }}đ</strong>
                                            </div>
                                        @endif
                                        @if ($payment['transferAmount'] > 0)
                                            <div class="payment-details-item">
                                                🏦 Chuyển khoản:
                                                <strong>{{ number_format($payment['transferAmount'], 0, ',', '.') }}đ</strong>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="payment-amount">
                                    Số tiền: <strong>{{ number_format($payment['amount'], 0, ',', '.') }}đ</strong>
                                </div>
                                <div class="payment-note">
                                    Ghi chú: {{ $payment['note'] }}
                                </div>
                            </div>
                            <div class="history-right">
                                <span class="badge badge-success">✓ Thành công</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    Chưa có lịch sử thanh toán
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Xuất lúc: {{ $ngayXuat }}</p>
            <p>Hệ thống quản lý phòng khám thú y - PETTY_VCMS</p>
        </div>
    </div>
</body>

</html>
