<?php namespace App\Http\Controllers;


use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NotificationController extends KoobeController
{

    public function index()
    {
        return view('notification/index');
    }

    public function all()
    {
        $connectedUser = $this->connectedUser;
        $this->notFoundIfNull($connectedUser);
        $userId = $connectedUser->id;
        $this->notFoundIfNull($userId);

        $notifications = Notification::whereUserId($userId)->orderBy("pushed_at")->get();
        return Response::json($notifications);
    }

    public function latest(){
        $connectedUser = $this->connectedUser;
        $this->notFoundIfNull($connectedUser);
        $userId = $connectedUser->id;
        $this->notFoundIfNull($userId);
        $notifications = Notification::whereUserId($userId)->whereReadedAt(null)->orderBy("pushed_at")->get();
        //todo update readed_at
        return Response::json($notifications);
    }

    public function delete(Request $request)
    {
        $id = $request->input("id");
        $this->notFoundIfNull($id);
        $connectedUser = $this->connectedUser;
        $this->notFoundIfNull($connectedUser);
        $userId = $connectedUser->id;
        $this->notFoundIfNull($userId);

        $success = Notification::whereId($id)->whereUserId($userId)->delete();

        return Response::json([
            "success" => $success
        ]);
    }
}
