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

    <form ng-submit="search.search()" id="search" class="input-group">
        <input type="text" ng-model="search.terms" class="form-control input-lg" placeholder="@lang('messages.home.search')">
        <span class="input-group-btn" id="themes">
            <select class="form-control input-lg" ng-model="search.theme" ng-change="search.search()">
                <option value="">@lang('messages.home.theme')</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                @endforeach
            </select>
        </span>
        <span class="input-group-btn" id="button">
            <button class="btn btn-default input-lg" type="submit"><span class="glyphicon glyphicon-search"></span></button>
        </span>
    </form>

    <ul id="books" masonry="" ng-hide="books.length == 0" reload-on-show>
        <li ng-repeat="book in books" class="masonry-brick well">
            <img class="cover" ng-src="covers/@{{book.slug}}.jpg">
            <span class="title">@{{book.title}}</span>
            <rating ng-model="book.average_rate" max="rating.max" readonly="rating.isReadonly"></rating>
            <a href="epubs/@{{book.slug}}.epub" class="pull-right"><span class="glyphicon glyphicon-download-alt"></span></a>
        </li>
    </ul>

    <div id="loading" class="well" ng-hide="!loadingMore"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>&nbsp;@lang('messages.home.loading')</div>
    <div id="no-results" class="well" ng-show="!loadingMore && books.length == 0">@lang('messages.home.noResults')</div>

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
            word-wrap: break-word;
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
        #loading {
            margin:20px auto;
            width:60%;
            text-align:center;
            font-size:16px;
        }
        #no-results {
            margin:20px auto;
            width:60%;
            text-align:center;
            font-size:16px;
        }
        #loading .glyphicon {
            margin-right:20px;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }
        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }
        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        #search {
            width:60%;
            margin:20px auto;
        }
        #search #themes {
            width:20%;
        }
    </style>
@stop

@section('scripts')

    {!! HTML::script('assets/javascript/angular.js') !!}
    {!! HTML::script('assets/javascript/masonry.js') !!}

    <script type="text/javascript">

        var koobeApp = angular.module('koobeApp', ['wu.masonry', 'ui.bootstrap']);


        koobeApp.controller('BooksCtrl', function ($scope, $http, $timeout) {

            $scope.reset = function () {
                $scope.books = [];
                $scope.loadingMore = false;
                $scope.page = 1;
                $scope.lastPage = null;
            }

            $scope.loadMoreBooks = function () {
                if ($scope.lastPage == null || $scope.page < $scope.lastPage && !$scope.loadingMore) {
                    $scope.loadingMore = true;
                    $timeout(function(){
                        $http.post("{{ URL::action('BookController@get') }}",{
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
