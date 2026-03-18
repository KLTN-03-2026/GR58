<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;

class NhanVienCreatedNotification extends Notification
{
    use Queueable;

    protected $nhanVien;
    protected $plainPassword;

    public function __construct($nhanVien, $plainPassword = null)
    {
        $this->nhanVien = $nhanVien;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['mail'];
        // Add database channel if notifications table exists
        if (Schema::hasTable('notifications')) {
            $channels[] = 'database';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Chào mừng nhân viên mới')
            ->greeting('Xin chào ' . $this->nhanVien->full_name)
            ->line('Tài khoản nhân viên của bạn đã được tạo.')
            ->line('Email: ' . $this->nhanVien->email);

        if ($this->plainPassword) {
            $mail->line('Mật khẩu tạm thời của bạn: ' . $this->plainPassword)
                ->line('Vui lòng đăng nhập và thay đổi mật khẩu ngay sau khi đăng nhập.');
        } else {
            $mail->line('Nếu bạn không nhập mật khẩu, hãy sử dụng chức năng quên mật khẩu để thiết lập.');
        }

        $mail->line('Cảm ơn bạn đã gia nhập đội ngũ.');

        return $mail;
    }

    /**
     * Get the array representation of the notification for database channel.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Tài khoản nhân viên mới',
            'message' => 'Tài khoản nhân viên ' . $this->nhanVien->full_name . ' đã được tạo.',
            'nhan_vien_id' => $this->nhanVien->id,
        ];
    }
}
