<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogFileActivity
{
    public function handle($event)
    {
        switch (true) {
            case $event instanceof \App\Events\FileUploaded:
                ActivityLogger::log(
                    'file_upload',
                    "Uploaded file: {$event->file->original_name}",
                    $event->user->id,
                    $event->file->id
                );
                break;

            case $event instanceof \App\Events\FileDownloaded:
                ActivityLogger::log(
                    'file_download',
                    "Downloaded file: {$event->file->original_name}",
                    $event->user->id,
                    $event->file->id
                );
                break;

            case $event instanceof \App\Events\FileViewed:
                ActivityLogger::log(
                    'file_view',
                    "Viewed file: {$event->file->original_name}",
                    $event->user->id,
                    $event->file->id
                );
                break;

            case $event instanceof \App\Events\FileShared:
                ActivityLogger::log(
                    'file_share',
                    "Shared file: {$event->file->original_name} with {$event->sharedWith}",
                    $event->user->id,
                    $event->file->id
                );
                break;
        }
    }
}