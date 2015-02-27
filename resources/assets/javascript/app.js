var koobeApp = angular.module('koobeApp', [
    'ngRoute',
    'koobeControllers',
    'wu.masonry',
    'ui.bootstrap'
]);

koobeApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/books', {
                templateUrl: 'partials/books.html',
                controller: 'BooksCtrl'
            }).
            when('/upload', {
                templateUrl: 'partials/upload.html',
                controller: 'UploadCtrl'
            }).
            when('/notifications', {
                templateUrl: 'partials/notifications.html',
                controller: 'NotificationCtrl'
            }).
            otherwise({
                redirectTo: '/books'
            });
    }]);