<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Phiếu Nhập Kho - {{ $phieu->ma_phieu_nhap }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #009689;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #009689;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 18px;
            color: #333;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 25px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            margin-bottom: 10px;
            display: flex;
        }

        .info-label {
            font-weight: bold;
            width: 180px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #222;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-cho-duyet {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-da-duyet {
            background-color: #d4edda;
            color: #155724;
        }

        .status-huy {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background-color: #009689;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #007d72;
        }

        table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            text-align: right;
        }

        .total-label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .total-value {
            font-size: 18px;
            font-weight: bold;
            color: #009689;
            margin-top: 5px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 80px;
            text-transform: uppercase;
        }

        .signature-name {
            font-style: italic;
        }

        .export-info {
            text-align: right;
            font-size: 10px;
            color: #888;
            margin-top: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #009689;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #009689;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>PHIẾU NHẬP KHO</h1>
        <h2>Mã phiếu: {{ $phieu->ma_phieu_nhap }}</h2>
    </div>

    <!-- Thông tin chung -->
    <div class="section-title">THÔNG TIN CHUNG</div>
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Ngày nhập:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($phieu->ngay_nhap)->format('d/m/Y') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nhà cung cấp:</div>
            <div class="info-value">
                {{ $phieu->nhaCungCap->ten_nha_cung_cap }} ({{ $phieu->nhaCungCap->ma_nha_cung_cap }})
            </div>
        </div>

        @if ($phieu->nhaCungCap->dia_chi)
            <div class="info-row">
                <div class="info-label">Địa chỉ NCC:</div>
                <div class="info-value">{{ $phieu->nhaCungCap->dia_chi }}</div>
            </div>
        @endif

        @if ($phieu->nhaCungCap->so_dien_thoai)
            <div class="info-row">
                <div class="info-label">Điện thoại NCC:</div>
                <div class="info-value">{{ $phieu->nhaCungCap->so_dien_thoai }}</div>
            </div>
        @endif

        @if ($phieu->nhanVien)
            <div class="info-row">
                <div class="info-label">Nhân viên nhập:</div>
                <div class="info-value">{{ $phieu->nhanVien->full_name }}</div>
            </div>
        @endif

        @if ($phieu->admin)
            <div class="info-row">
                <div class="info-label">Admin phê duyệt:</div>
                <div class="info-value">{{ $phieu->admin->ho_ten }}</div>
            </div>
        @endif


        @if ($phieu->phieuChi)
            <div class="info-row">
                <div class="info-label">Mã phiếu chi:</div>
                <div class="info-value">{{ $phieu->phieuChi->ma_phieu_chi }}</div>
            </div>
        @endif

        @if ($phieu->ghi_chu)
            <div class="info-row">
                <div class="info-label">Ghi chú:</div>
                <div class="info-value">{{ $phieu->ghi_chu }}</div>
            </div>
        @endif
    </div>

    <!-- Bảng chi tiết hàng hóa -->
    <div class="section-title">CHI TIẾT HÀNG HÓA</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">STT</th>
                <th style="width: 35%;">Tên hàng hóa</th>
                <th style="width: 10%;" class="text-center">Số lô</th>
                <th style="width: 8%;" class="text-center">ĐVT</th>
                <th style="width: 10%;" class="text-right">Số lượng</th>
                <th style="width: 12%;" class="text-right">Đơn giá</th>
                <th style="width: 10%;" class="text-center">HSD</th>
                <th style="width: 10%;" class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($phieu->chiTietPhieuNhapKhos as $index => $chiTiet)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $chiTiet->hangHoa->ten_mat_hang }}</strong>
                        @if (isset($chiTiet->hangHoa->ma_hang_hoa))
                            <br><small style="color: #666;">Mã: {{ $chiTiet->hangHoa->ma_hang_hoa }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $chiTiet->so_lo }}</td>
                    <td class="text-center">{{ $chiTiet->hangHoa->don_vi_tinh }}</td>
                    <td class="text-right">{{ number_format($chiTiet->so_luong, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($chiTiet->don_gia, 0, ',', '.') }} đ</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($chiTiet->han_su_dung)->format('d/m/Y') }}</td>
                    <td class="text-right"><strong>{{ number_format($chiTiet->thanh_tien, 0, ',', '.') }} đ</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tổng tiền -->
    <div class="total-section">
        <div class="total-label">TỔNG CỘNG:</div>
        <div class="total-value">{{ number_format($phieu->tong_tien, 0, ',', '.') }} VNĐ</div>
    </div>

    <!-- Chữ ký -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Người lập phiếu</div>
                <div class="signature-name">
                    @if ($phieu->nhanVien)
                        {{ $phieu->nhanVien->full_name }}
                    @elseif($phieu->admin)
                        {{ $phieu->admin->ho_ten }}
                    @endif
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Thủ kho</div>
                <div class="signature-name">(Ký, ghi rõ họ tên)</div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Giám đốc</div>
                <div class="signature-name">(Ký, đóng dấu)</div>
            </div>
        </div>
    </div>

    <!-- Thông tin xuất file -->
    <div class="export-info">
        Xuất ngày: {{ $ngay_xuat }} | Hệ thống quản lý phòng khám thú y
    </div>
</body>

</html>
