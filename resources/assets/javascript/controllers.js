var koobeControllers = angular.module('koobeControllers', []);

koobeControllers.controller('BooksCtrl', ['$scope', '$http', '$timeout',
    function ($scope, $http, $timeout) {
        $scope.reset = function () {
            $scope.books = [];
            $scope.loadingMore = false;
            $scope.page = 1;
            $scope.lastPage = null;
        }

        $scope.loadMoreBooks = function () {
            if (!$scope.loadingMore && ($scope.lastPage == null || $scope.page < $scope.lastPage)) {
                $scope.loadingMore = true;
                $timeout(function(){
                    $http.post("/books",{
                        page: $scope.page,
                        terms: $scope.criteria.terms,
                        theme: $scope.criteria.theme
                    }).success(function (page) {
                        $scope.books = $scope.books.concat(page.data);
                        $scope.lastPage = page.last_page;
                        $scope.loadingMore = false;
                        $scope.page++;
                    });
                }, $scope.page > 1 ? 500 : 0);
            }
        }
        $scope.search = {};
        $scope.criteria = {};
        $scope.search.search = function () {
            $scope.criteria.terms = $scope.search.terms;
            $scope.criteria.theme = $scope.search.theme;
            $scope.reset();
            $scope.loadMoreBooks();
        }

        $scope.reset();

        $scope.loadMoreBooks();

        $scope.rating = {
            isReadonly: true,
            max: 5
        }
    }]);

koobeControllers.controller('UploadCtrl', ['$scope', '$http',
    function ($scope, $http) {
    }]);

koobeControllers.controller('NotificationCtrl', ['$scope', '$http',
    function ($scope, $http) {
    }]);
