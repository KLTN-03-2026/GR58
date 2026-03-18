<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;

class LichLamViecCreatedNotification extends Notification
{
    use Queueable;

    protected $lich;

    public function __construct($lich)
    {
        $this->lich = $lich;
    }

    public function via($notifiable)
    {
        $channels = ['mail'];
        if (Schema::hasTable('notifications')) {
            $channels[] = 'database';
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        $line = 'Lịch làm việc đã được tạo cho bạn vào ngày ' . $this->lich->ngay_lam;
        return (new MailMessage)
            ->subject('Thông báo lịch làm việc')
            ->greeting('Xin chào ' . ($notifiable->full_name ?? ''))
            ->line($line)
            ->line('Phòng trực: ' . ($this->lich->phong_truc ?? ''))
            ->line('Ca: ' . ($this->lich->thoi_gian_truc ?? ''));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Lịch làm việc mới',
            'message' => 'Lịch làm việc của bạn vào ' . $this->lich->ngay_lam . ' đã được tạo.',
            'lich_id' => $this->lich->id,
        ];
    }
}
