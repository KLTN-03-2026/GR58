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
        $name     = $this->nhanVien->full_name;
        $email    = $this->nhanVien->email;
        $password = $this->plainPassword ?? '(đã được thiết lập)';
        $vaiTro   = match($this->nhanVien->vai_tro ?? '') {
            'bac_si' => 'Bác sĩ',
            'y_ta'   => 'Y tá',
            default  => 'Nhân viên',
        };
        $loginUrl = config('app.url', 'http://localhost:5173') . '/staff/login';

        return (new MailMessage)
            ->subject('🎉 Chào mừng bạn gia nhập đội ngũ Petty Care!')
            ->view('emails.nhan_vien_created', compact('name', 'email', 'password', 'vaiTro', 'loginUrl'));
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
