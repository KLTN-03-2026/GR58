<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chào mừng gia nhập Petty Care</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background-color: #f4f7f6; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
    .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }

    /* Header */
    .header { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); padding: 40px 32px; text-align: center; }
    .header .logo { font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: 1px; }
    .header .logo span { color: #99f6e4; }
    .header .tagline { color: #ccfbf1; font-size: 13px; margin-top: 4px; }

    /* Banner */
    .banner { background: linear-gradient(135deg, #134e4a 0%, #0d9488 100%); padding: 32px; text-align: center; }
    .banner .emoji { font-size: 52px; display: block; margin-bottom: 12px; }
    .banner h1 { color: #ffffff; font-size: 24px; font-weight: 700; line-height: 1.3; }
    .banner p { color: #99f6e4; font-size: 14px; margin-top: 8px; }

    /* Body */
    .body { padding: 36px 32px; }
    .greeting { font-size: 16px; color: #1f2937; margin-bottom: 16px; }
    .greeting strong { color: #0d9488; }
    .intro { font-size: 14px; color: #4b5563; line-height: 1.7; margin-bottom: 28px; }

    /* Info card */
    .info-card { background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; }
    .info-card .title { font-size: 12px; font-weight: 600; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; }
    .info-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .info-row:last-child { margin-bottom: 0; }
    .info-label { font-size: 13px; color: #6b7280; min-width: 110px; }
    .info-value { font-size: 13px; color: #111827; font-weight: 600; }
    .badge { display: inline-block; background: #0d9488; color: #fff; font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px; }

    /* Password box */
    .password-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 20px; margin-bottom: 28px; }
    .password-box .pw-label { font-size: 12px; color: #92400e; font-weight: 600; margin-bottom: 6px; }
    .password-box .pw-value { font-size: 20px; font-weight: 800; color: #78350f; letter-spacing: 3px; font-family: monospace; }
    .password-box .pw-note { font-size: 12px; color: #b45309; margin-top: 8px; }

    /* CTA Button */
    .cta-wrap { text-align: center; margin-bottom: 28px; }
    .cta-btn { display: inline-block; background: linear-gradient(135deg, #0d9488, #0f766e); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; padding: 14px 36px; border-radius: 10px; letter-spacing: 0.3px; }

    /* Steps */
    .steps { margin-bottom: 28px; }
    .steps .step-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 12px; }
    .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px; }
    .step-num { background: #0d9488; color: #fff; font-size: 11px; font-weight: 700; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
    .step-text { font-size: 13px; color: #4b5563; line-height: 1.5; }

    /* Footer */
    .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 32px; text-align: center; }
    .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
    .footer .brand { font-weight: 700; color: #0d9488; }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- Header -->
    <div class="header">
      <div class="logo">🐾 Petty<span>Care</span></div>
      <div class="tagline">Hệ thống quản lý phòng khám thú y</div>
    </div>

    <!-- Banner -->
    <div class="banner">
      <span class="emoji">🎉</span>
      <h1>Chào mừng bạn gia nhập<br>đội ngũ Petty Care!</h1>
      <p>Tài khoản của bạn đã được tạo thành công</p>
    </div>

    <!-- Body -->
    <div class="body">
      <p class="greeting">Xin chào <strong>{{ $name }}</strong>,</p>
      <p class="intro">
        Chúng tôi rất vui mừng chào đón bạn trở thành thành viên của đội ngũ <strong>Petty Care</strong>!
        Dưới đây là thông tin tài khoản để bạn bắt đầu làm việc trên hệ thống.
      </p>

      <!-- Thông tin tài khoản -->
      <div class="info-card">
        <div class="title">📋 Thông tin tài khoản</div>
        <div class="info-row">
          <span class="info-label">Họ và tên</span>
          <span class="info-value">{{ $name }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email đăng nhập</span>
          <span class="info-value">{{ $email }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Vai trò</span>
          <span class="badge">{{ $vaiTro }}</span>
        </div>
      </div>

      <!-- Mật khẩu -->
      <div class="password-box">
        <div class="pw-label">🔑 Mật khẩu đăng nhập lần đầu</div>
        <div class="pw-value">{{ $password }}</div>
        <div class="pw-note">⚠️ Vui lòng đổi mật khẩu ngay sau khi đăng nhập lần đầu để bảo mật tài khoản.</div>
      </div>

      <!-- CTA -->
      <div class="cta-wrap">
        <a href="{{ $loginUrl }}" class="cta-btn">🚀 Đăng nhập ngay</a>
      </div>

      <!-- Hướng dẫn -->
      <div class="steps">
        <div class="step-title">Các bước bắt đầu:</div>
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-text">Truy cập đường dẫn đăng nhập và nhập email + mật khẩu ở trên</div>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <div class="step-text">Vào phần <strong>Hồ sơ cá nhân</strong> và đổi mật khẩu mới</div>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <div class="step-text">Cập nhật thông tin cá nhân và ảnh đại diện của bạn</div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>Email này được gửi tự động từ hệ thống <span class="brand">Petty Care</span>.<br>
      Nếu bạn có thắc mắc, vui lòng liên hệ quản trị viên.<br>
      © {{ date('Y') }} Petty Care. All rights reserved.</p>
    </div>

  </div>
</body>
</html>
