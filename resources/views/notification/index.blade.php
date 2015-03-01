@extends('layouts.master', [
	"htmlTagAttrs" => [
		"ng-app" => "koobeApp",
		"ng-controller" => "NotificationsCtrl"
	]
])

@section('content')
    <ul>
        <li ng-repeat="notification in notifications" class="alert" ng-class="{ERROR:'alert-danger', WARNING:'alert-warning', 'INFO':'alert-info'}[notification.type]" role="alert">
            <button type="button" class="close" ng-click="deleteNotification($index, notification.id)"><span aria-hidden="true">&times;</span></button>
            <span>@{{notification.pushed_at}}</span>
            <span>@{{notification.message}}</span>
        </li>
    </ul>
@stop

@section('scripts')

    {!! HTML::script('assets/javascript/angular.js') !!}

    <script type="text/javascript">

        var koobeApp = angular.module('koobeApp', []);


        koobeApp.controller('NotificationsCtrl', function ($scope, $http) {
            $http.get("{{ URL::action('NotificationController@all') }}").success(function (notifications) {
                $scope.notifications = notifications;
            });

            $scope.deleteNotification = function(index, notificationId){
                $http.get("{{ URL::action('NotificationController@delete') }}",{
                    params: {
                        id: notificationId
                    }
                }).success(function (data) {
                    if(data.success){
                        $scope.notifications.splice(index, 1);
                    }else{
                        //todo throw error ?
                    }
                });
            };
        });
    </script>
@stop