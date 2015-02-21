<?php namespace App\Http\Controllers;


use App\Models\Notification;

class NotificationController extends KoobeController
{

    public function all()
    {
        $connectedUser = $this->connectedUser;
        $this->notFoundIfNull($connectedUser);
        $this->notFoundIfNull($connectedUser->id);

        $notifications = Notification::whereUserId($connectedUser->id)->get();

        return view('notification/all', ['notifications' => $notifications]);
    }

}
