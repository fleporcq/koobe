<?php namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationType;
use InvalidArgumentException;

abstract class Notifier
{

    public static function push($user, $message = null, $type = null)
    {
        if ($user != null && $user->id != null && !empty($message) && $type != null) {
            Notification::create([
                "user_id" => $user->id,
                "message" => $message,
                "type" => $type
            ]);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public static function info($user, $message = null)
    {
        self::push($user, $message, NotificationType::INFO);
    }

    public static function warning($user, $message = null)
    {
        self::push($user, $message, NotificationType::WARNING);
    }

    public static function error($user, $message = null)
    {
        self::push($user, $message, NotificationType::ERROR);
    }
}