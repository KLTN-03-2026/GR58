<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Chi - {{ $phieuChi->ma_phieu_chi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #009689;
        }

        .header h1 {
            font-size: 28px;
            color: #009689;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header .code {
            font-size: 16px;
            color: #666;
            font-weight: bold;
        }

        .header .date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        /* Info Section */
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .info-section h2 {
            font-size: 16px;
            color: #009689;
            margin-bottom: 15px;
            border-bottom: 2px solid #009689;
            padding-bottom: 5px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #666;
            padding: 8px 15px 8px 0;
            width: 40%;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #333;
            vertical-align: top;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge.success {
            background-color: #d1fae5;
            color: #059669;
        }

        .badge.warning {
            background-color: #ffedd4;
            color: #ca3500;
        }

        .badge.blue {
            background-color: #dbeafe;
            color: #1447e6;
        }

        .badge.yellow {
            background-color: #fef9c2;
            color: #a65f00;
        }

        /* Summary Box */
        .summary-box {
            margin: 20px 0;
            padding: 15px;
            background-color: #fef2f2;
            border: 2px solid #ffc9c9;
            border-radius: 8px;
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
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: bold;
        }

        .summary-value.total {
            color: #101828;
        }

        .summary-value.paid {
            color: #00a63e;
        }

        .summary-value.debt {
            color: #e7000b;
        }

        /* Payment History Table */
        .payment-history {
            margin: 20px 0;
        }

        .payment-history h2 {
            font-size: 16px;
            color: #009689;
            margin-bottom: 15px;
            border-bottom: 2px solid #009689;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background-color: white;
        }

        table th {
            background-color: #009689;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .amount {
            font-weight: bold;
            color: #00a63e;
        }

        .small-text {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            text-align: center;
            width: 50%;
            padding: 10px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .signature-name {
            font-style: italic;
            margin-top: 10px;
        }

        .export-info {
            margin-top: 20px;
            font-size: 11px;
            color: #999;
        }

        /* No payment history */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PHIẾU CHI</h1>
            <div class="code">{{ $phieuChi->ma_phieu_chi }}</div>
            <div class="date">Ngày xuất: {{ $ngayXuat }}</div>
        </div>

        <!-- Thông tin cơ bản -->
        <div class="info-section">
            <h2>THÔNG TIN PHIẾU CHI</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Loại chi:</div>
                    <div class="info-value">
                        <span class="badge {{ $phieuChi->loai_phieu_chi === 'chi_nhap_hang' ? 'blue' : 'yellow' }}">
                            {{ $phieuChi->loai_phieu_chi_label }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nội dung chi:</div>
                    <div class="info-value">{{ $phieuChi->ly_do_chi }}</div>
                </div>
                @if ($phieuChi->nhaCungCap)
                    <div class="info-row">
                        <div class="info-label">Nhà cung cấp:</div>
                        <div class="info-value">{{ $phieuChi->nhaCungCap->ten_nha_cung_cap }}</div>
                    </div>
                @endif
                @if ($phieuChi->doi_tuong_nhan_tien)
                    <div class="info-row">
                        <div class="info-label">Đối tượng nhận tiền:</div>
                        <div class="info-value">{{ $phieuChi->doi_tuong_nhan_tien }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Ngày chi:</div>
                    <div class="info-value">{{ $phieuChi->ngay_chi->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Người tạo:</div>
                    <div class="info-value">{{ $phieuChi->nguoi_tao_info['ten'] ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Trạng thái:</div>
                    <div class="info-value">
                        <span class="badge {{ $phieuChi->trang_thai === 'da_hoan_thanh' ? 'success' : 'warning' }}">
                            {{ $phieuChi->trang_thai_label }}
                        </span>
                    </div>
                </div>
                @if ($phieuChi->ghi_chu)
                    <div class="info-row">
                        <div class="info-label">Ghi chú:</div>
                        <div class="info-value">{{ $phieuChi->ghi_chu }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tổng quan tài chính -->
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Tổng giá trị</div>
                    <div class="summary-value total">{{ number_format($phieuChi->tong_so_tien, 0, ',', '.') }}đ</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Đã trả</div>
                    <div class="summary-value paid">
                        {{ number_format($phieuChi->so_tien_thanh_toan_ngay, 0, ',', '.') }}đ</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Còn nợ</div>
                    <div class="summary-value debt">{{ number_format($phieuChi->so_tien_con_no, 0, ',', '.') }}đ</div>
                </div>
            </div>
        </div>

        <!-- Lịch sử thanh toán -->
        <div class="payment-history">
            <h2>LỊCH SỬ THANH TOÁN</h2>

            @if ($phieuChi->lichSuThanhToan && $phieuChi->lichSuThanhToan->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">Lần</th>
                            <th style="width: 18%;">Ngày giờ</th>
                            <th style="width: 22%;">Hình thức</th>
                            <th style="width: 20%;">Số tiền</th>
                            <th style="width: 32%;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($phieuChi->lichSuThanhToan as $index => $lichSu)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                                <td>{{ $lichSu->ngay_thanh_toan->format('H:i d/m/Y') }}</td>
                                <td>
                                    {{ $lichSu->hinh_thuc_thanh_toan_label }}
                                    @if ($lichSu->hinh_thuc_thanh_toan === 'ca_hai')
                                        <div class="small-text">
                                            Tiền mặt: {{ number_format($lichSu->tien_mat, 0, ',', '.') }}đ<br>
                                            CK: {{ number_format($lichSu->tien_chuyen_khoan, 0, ',', '.') }}đ
                                        </div>
                                    @endif
                                </td>
                                <td class="amount">{{ number_format($lichSu->so_tien_thanh_toan, 0, ',', '.') }}đ</td>
                                <td class="small-text">{{ $lichSu->ghi_chu ?? 'Không có ghi chú' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    Chưa có lịch sử thanh toán
                </div>
            @endif
        </div>

        <!-- Chữ ký -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Người lập phiếu</div>
                <div class="signature-name">(Ký và ghi rõ họ tên)</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Kế toán</div>
                <div class="signature-name">(Ký và ghi rõ họ tên)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="export-info">
                File được tạo tự động từ hệ thống quản lý phòng khám thú y<br>
                Thời gian xuất: {{ $ngayXuat }}
            </div>
        </div>
    </div>
</body>

</html>
