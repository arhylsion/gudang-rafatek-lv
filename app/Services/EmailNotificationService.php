<?php

namespace App\Services;

use App\Mail\BarangKeluarNotification;
use App\Mail\StatusUpdateNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    public static function sendOutgoingGoodsNotification(array $data): bool
    {
        try {
            $to = $data['user_email'] ?? null;
            if ($to) {
                Mail::to($to, $data['user_name'] ?? null)
                    ->cc(config('mail.admin_address', env('MAIL_ADMIN_ADDRESS')))
                    ->send(new BarangKeluarNotification($data));
            } else {
                Mail::to(config('mail.admin_address', env('MAIL_ADMIN_ADDRESS')))
                    ->send(new BarangKeluarNotification($data));
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('Email error: '.$e->getMessage());
            return false;
        }
    }

    public static function sendStatusUpdateNotification(array $data): bool
    {
        try {
            $to = $data['user_email'] ?? null;
            if ($to) {
                Mail::to($to, $data['user_name'] ?? null)
                    ->cc(config('mail.admin_address', env('MAIL_ADMIN_ADDRESS')))
                    ->send(new StatusUpdateNotification($data));
            } else {
                Mail::to(config('mail.admin_address', env('MAIL_ADMIN_ADDRESS')))
                    ->send(new StatusUpdateNotification($data));
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('Status update email error: '.$e->getMessage());
            return false;
        }
    }
}
