<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    /**
     * The contact message instance.
     */
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Message: ' . $this->message->subject)
            ->greeting('Hello!')
            ->line('You have received a new contact message from ' . $this->message->name . '.')
            ->line('Subject: ' . $this->message->subject)
            ->line('Category: ' . ($this->message->category ? $this->message->category->name : 'None'))
            ->action('View Message', url(route('admin.messages.show', $this->message->id)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender' => $this->message->name,
            'subject' => $this->message->subject,
            'category' => $this->message->category ? $this->message->category->name : null,
            'time' => $this->message->created_at->format('Y-m-d H:i:s'),
            'link' => route('admin.messages.show', $this->message->id)
        ];
    }
}
