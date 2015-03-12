var notifier = angular.module('notifier', ['cgNotify']).run(function($interval, $http, notify) {
    var displayNotifications = function(){
        $http.get("/notifications/latest").success(function (notifications) {
            angular.forEach(notifications, function(notification) {
                notify(notification.message);
            });
        });
    };

    $interval(function(){
        displayNotifications();
    },5000);

    displayNotifications();

});