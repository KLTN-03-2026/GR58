<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác thực tài khoản Petty',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <style>
                        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; background-color: #f4f4f7; color: #51545e; margin: 0; padding: 0; }
                        .email-wrapper { width: 100%; background-color: #f4f4f7; padding: 20px; }
                        .email-content { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                        .email-header { background-color: #2F5755; padding: 20px; text-align: center; color: #ffffff; }
                        .email-header h1 { margin: 0; font-size: 24px; font-weight: bold; }
                        .email-body { padding: 30px; }
                        .email-body p { line-height: 1.6; margin-bottom: 20px; }
                        .button { display: inline-block; background-color: #2F5755; color: #ffffff !important; padding: 12px 25px; text-decoration: none !important; border-radius: 5px; font-weight: bold; text-align: center; }
                        .email-footer { padding: 20px; text-align: center; font-size: 12px; color: #6b6e76; background-color: #f4f4f7; }
                    </style>
                </head>
                <body>
                    <div class="email-wrapper">
                        <div class="email-content">
                            <div class="email-header">
                                <h1>PETTY</h1>
                            </div>
                            <div class="email-body">
                                <p>Chào bạn,</p>
                                <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Petty</strong>. Để bắt đầu sử dụng dịch vụ, vui lòng xác thực địa chỉ email của bạn bằng cách nhấn vào nút bên dưới:</p>
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="' . $this->url . '" class="button">Xác thực Tài khoản</a>
                                </div>
                                <p>Nếu nút trên không hoạt động, bạn có thể copy và dán đường dẫn sau vào trình duyệt:</p>
                                <p style="word-break: break-all; color: #3869d4; font-size: 14px;">' . $this->url . '</p>
                                <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
                                <p>Trân trọng,<br>Đội ngũ Petty</p>
                            </div>
                        </div>
                        <div class="email-footer">
                            <p>&copy; ' . date('Y') . ' Petty. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
            ',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
