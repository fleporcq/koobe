<?php namespace App\Models;

class NotificationType extends Enum {
    const INFO = "info";
    const WARNING = "warning";
    const ERROR = "error";
    const SUCCESS = "success";
}