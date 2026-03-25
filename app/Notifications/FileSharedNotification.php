<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $file;
    protected $sharedBy;
    protected $share;

    public function __construct($file, $sharedBy, $share)
    {
        $this->file = $file;
        $this->sharedBy = $sharedBy;
        $this->share = $share;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('shared.show', $this->share->share_token);

        return (new MailMessage)
            ->subject('File Shared With You')
            ->greeting('Hello!')
            ->line("{$this->sharedBy->name} has shared a file with you.")
            ->line("**File:** {$this->file->original_name}")
            ->line("**Permissions:** " . implode(', ', $this->share->permissions))
            ->line("**Valid Until:** " . $this->share->valid_until->format('F j, Y H:i'))
            ->action('Access File', $url)
            ->line('Thank you for using our DMS!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'file_shared',
            'file_id' => $this->file->id,
            'file_name' => $this->file->original_name,
            'shared_by_id' => $this->sharedBy->id,
            'shared_by_name' => $this->sharedBy->name,
            'share_id' => $this->share->id,
            'share_token' => $this->share->share_token,
            'permissions' => $this->share->permissions,
            'valid_until' => $this->share->valid_until,
        ];
    }
}