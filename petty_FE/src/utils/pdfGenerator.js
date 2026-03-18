/**
 * Generate PDF cho phiếu nhập kho sử dụng window.print()
 * Không cần thư viện bên ngoài, sử dụng native browser print
 */

export const generatePhieuNhapKhoPdf = (data) => {
  // Format currency
  const formatCurrency = (amount) => {
    return `${(amount || 0).toLocaleString("vi-VN")} ₫`;
  };

  // Format date
  const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  };

  // Tính tổng số lượng
  const totalQuantity = data.chi_tiet.reduce(
    (sum, item) => sum + (item.so_luong || 0),
    0
  );

  // Người nhập
  const nguoiNhap = data.nhan_vien?.full_name || data.admin?.ho_ten || "N/A";

  // Tạo HTML cho PDF
  const htmlContent = `
    <!DOCTYPE html>
    <html lang="vi">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Phiếu Nhập Kho - ${data.ma_phieu_nhap}</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        
        body {
          font-family: 'Times New Roman', Times, serif;
          font-size: 13px;
          line-height: 1.6;
          color: #000;
          padding: 20mm;
          background: white;
        }
        
        @media print {
          body {
            padding: 10mm;
          }
          
          .no-print {
            display: none !important;
          }
          
          table {
            page-break-inside: auto;
          }
          
          tr {
            page-break-inside: avoid;
            page-break-after: auto;
          }
        }
        
        .header {
          text-align: center;
          margin-bottom: 30px;
        }
        
        .header h1 {
          font-size: 24px;
          font-weight: bold;
          text-transform: uppercase;
          margin-bottom: 10px;
          color: #000;
        }
        
        .header .subtitle {
          font-size: 14px;
          color: #666;
          font-style: italic;
        }
        
        .info-section {
          margin-bottom: 25px;
        }
        
        .info-row {
          display: flex;
          margin-bottom: 8px;
        }
        
        .info-label {
          font-weight: bold;
          min-width: 150px;
          color: #333;
        }
        
        .info-value {
          color: #000;
        }
        
        table {
          width: 100%;
          border-collapse: collapse;
          margin: 20px 0;
        }
        
        table thead {
          background-color: #f5f5f5;
        }
        
        table th {
          border: 1px solid #ddd;
          padding: 10px 8px;
          text-align: left;
          font-weight: bold;
          font-size: 12px;
          text-transform: uppercase;
        }
        
        table td {
          border: 1px solid #ddd;
          padding: 8px;
          font-size: 12px;
        }
        
        table tbody tr:nth-child(even) {
          background-color: #fafafa;
        }
        
        .text-right {
          text-align: right;
        }
        
        .text-center {
          text-align: center;
        }
        
        .summary {
          margin-top: 30px;
          padding-top: 15px;
          border-top: 2px solid #000;
        }
        
        .summary-row {
          display: flex;
          justify-content: flex-end;
          margin-bottom: 10px;
          font-size: 14px;
        }
        
        .summary-label {
          font-weight: bold;
          min-width: 200px;
          text-align: right;
          padding-right: 20px;
        }
        
        .summary-value {
          min-width: 150px;
          text-align: right;
          font-weight: bold;
        }
        
        .total-amount {
          font-size: 16px;
          color: #009689;
          margin-top: 10px;
          padding-top: 10px;
          border-top: 1px solid #ddd;
        }
        
        .signatures {
          margin-top: 50px;
          display: flex;
          justify-content: space-between;
        }
        
        .signature-box {
          text-align: center;
          width: 45%;
        }
        
        .signature-title {
          font-weight: bold;
          margin-bottom: 60px;
          text-transform: uppercase;
        }
        
        .signature-name {
          font-style: italic;
        }
        
        .footer {
          margin-top: 30px;
          padding-top: 15px;
          border-top: 1px solid #ddd;
          text-align: center;
          font-size: 11px;
          color: #666;
        }
        
        .print-button {
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 10px 20px;
          background-color: #009689;
          color: white;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          font-size: 14px;
          font-weight: bold;
          z-index: 1000;
        }
        
        .print-button:hover {
          background-color: #007d72;
        }
        
        .notes {
          margin-top: 20px;
          padding: 10px;
          background-color: #f9f9f9;
          border-left: 3px solid #009689;
        }
        
        .notes-title {
          font-weight: bold;
          margin-bottom: 5px;
        }
      </style>
    </head>
    <body>
      <button class="print-button no-print" onclick="window.print()">
        🖨️ In Phiếu
      </button>
      
      <!-- Header -->
      <div class="header">
        <h1>PHIẾU NHẬP KHO</h1>
        <div class="subtitle">Mã phiếu: ${data.ma_phieu_nhap}</div>
      </div>
      
      <!-- Thông tin chung -->
      <div class="info-section">
        <div class="info-row">
          <span class="info-label">Nhà cung cấp:</span>
          <span class="info-value">${data.nha_cung_cap.ten_nha_cung_cap}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Mã nhà cung cấp:</span>
          <span class="info-value">${data.nha_cung_cap.ma_nha_cung_cap}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Địa chỉ:</span>
          <span class="info-value">${data.nha_cung_cap.dia_chi || "N/A"}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Điện thoại:</span>
          <span class="info-value">${
            data.nha_cung_cap.so_dien_thoai || "N/A"
          }</span>
        </div>
        <div class="info-row">
          <span class="info-label">Ngày nhập:</span>
          <span class="info-value">${formatDate(data.ngay_nhap)}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Người nhập:</span>
          <span class="info-value">${nguoiNhap}</span>
        </div>
      </div>
      
      <!-- Bảng chi tiết hàng hóa -->
      <table>
        <thead>
          <tr>
            <th class="text-center" style="width: 40px;">STT</th>
            <th style="width: 200px;">Tên hàng hóa</th>
            <th class="text-center" style="width: 60px;">ĐVT</th>
            <th class="text-right" style="width: 80px;">Số lượng</th>
            <th class="text-right" style="width: 100px;">Đơn giá</th>
            <th style="width: 120px;">Lô / HSD</th>
            <th class="text-right" style="width: 120px;">Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          ${data.chi_tiet
            .map(
              (item, index) => `
            <tr>
              <td class="text-center">${index + 1}</td>
              <td>
                <strong>${item.hang_hoa.ten_mat_hang}</strong><br>
                <small style="color: #666;">${item.hang_hoa.ma_hang_hoa}</small>
              </td>
              <td class="text-center">${item.hang_hoa.don_vi_tinh}</td>
              <td class="text-right">${item.so_luong}</td>
              <td class="text-right">${formatCurrency(item.don_gia)}</td>
              <td>
                <strong>Lô:</strong> ${item.so_lo}<br>
                <strong>HSD:</strong> ${formatDate(item.han_su_dung)}
              </td>
              <td class="text-right"><strong>${formatCurrency(
                item.thanh_tien
              )}</strong></td>
            </tr>
          `
            )
            .join("")}
        </tbody>
      </table>
      
      <!-- Tổng kết -->
      <div class="summary">
        <div class="summary-row">
          <span class="summary-label">Tổng số lượng:</span>
          <span class="summary-value">${totalQuantity} sản phẩm</span>
        </div>
        <div class="summary-row total-amount">
          <span class="summary-label">TỔNG TIỀN HÀNG:</span>
          <span class="summary-value">${formatCurrency(data.tong_tien)}</span>
        </div>
      </div>
      
      <!-- Ghi chú nếu có -->
      ${
        data.ghi_chu
          ? `
      <div class="notes">
        <div class="notes-title">Ghi chú:</div>
        <div>${data.ghi_chu}</div>
      </div>
      `
          : ""
      }
      
      <!-- Chữ ký -->
      <div class="signatures">
        <div class="signature-box">
          <div class="signature-title">Người lập phiếu</div>
          <div class="signature-name">${nguoiNhap}</div>
        </div>
        <div class="signature-box">
          <div class="signature-title">Người duyệt</div>
          <div class="signature-name">(Ký và ghi rõ họ tên)</div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="footer">
        <div>Ngày xuất: ${data.ngay_xuat}</div>
        <div style="margin-top: 5px;">
          Phiếu nhập kho - Hệ thống quản lý phòng khám thú y
        </div>
      </div>
    </body>
    </html>
  `;

  // Mở cửa sổ mới để in
  const printWindow = window.open("", "_blank");
  if (printWindow) {
    printWindow.document.write(htmlContent);
    printWindow.document.close();

    // Đợi load xong rồi focus vào window để sẵn sàng in
    printWindow.onload = function () {
      printWindow.focus();
    };
  } else {
    throw new Error("Không thể mở cửa sổ in. Vui lòng kiểm tra popup blocker.");
  }
};
