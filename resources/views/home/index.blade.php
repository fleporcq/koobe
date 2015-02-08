@extends('layouts.master',array(
	"htmlTagAttrs" => array(
		"ng-app" => "koobeApp",
		"ng-controller" => "BooksCtrl"
	),
	"bodyTagAttrs" => array(
		"when-scrolled" => "loadMoreBooks()"
	)
))
@section('content')
    <ul id="books" masonry="">
        <li ng-repeat="book in books" class="masonry-brick well">
            <img class="cover" ng-src="covers/@{{book.slug}}.jpg">
            <span class="title">@{{book.title}}</span>
            <rating ng-model="book.average_rate" max="rating.max" readonly="rating.isReadonly"></rating>
            <a href="epubs/@{{book.slug}}.epub" class="pull-right"><span class="glyphicon glyphicon-download-alt"></span></a>
        </li>
    </ul>
@stop

@section('styles')
    <style type="text/css">
        ul#books {
            padding: 0;
            margin: 0 auto;
            list-style: none;
        }

        ul#books > li {
            width: 16%;
            min-width: 150px;

        }

        ul#books > li:hover {
            transition: all .2s ease-in-out;
            transform: scale(1.1);
        }

        @media only screen and (max-width: 600px) {
            ul#books > li {
                width: 42%;
            }
        }

        ul#books span.title {
            display: block;
            margin: 10px 0 5px 0;
            text-align: center;
        }

        ul#books img.cover {
            max-width: 100%;
            height: auto;
        }

        .masonry-brick {
            margin: 1em;
            display: none;
        }

        .masonry-brick.loaded {
            display: block;
        }
    </style>
@stop

@section('scripts')

    {!! HTML::script('assets/javascript/angular.js') !!}
    {!! HTML::script('assets/javascript/masonry.js') !!}

    <script type="text/javascript">

        var koobeApp = angular.module('koobeApp', ['wu.masonry', 'ui.bootstrap']);


        koobeApp.controller('BooksCtrl', function ($scope, $http) {
            $scope.books = [];

            $scope.page = 1;
            $scope.lastPage = null;
            $scope.loadMoreBooks = function () {
                if ($scope.lastPage == null || $scope.page <= $scope.lastPage) {
                    $http.get('books?page=' + $scope.page).success(function (page) {
                        $scope.books = $scope.books.concat(page.data);
                        $scope.lastPage = page.last_page;
                    });
                    $scope.page++;

                }
            }
            $scope.loadMoreBooks();

            $scope.rating = {
                isReadonly: true,
                max: 5
            }

        });


        koobeApp.directive('whenScrolled', function ($document) {
            return {
                restrict: 'A',
                scope: {
                    whenScrolled: "&"
                },
                link: function (scope, elem, attrs) {
                    rawElement = elem[0];
                    $(window).bind('scroll', function () {
                        if ($(window).scrollTop() + $(window).height() + 5 >= $document.height()) {
                            scope.$apply(scope.whenScrolled);
                        }
                    });
                }
            };
        });


    </script>
@stop
