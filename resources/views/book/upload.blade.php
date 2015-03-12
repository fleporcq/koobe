@extends('layouts.master',[
	"htmlTagAttrs" => [
		"ng-app" => "koobeApp"
	]
])
@section('content')
    <div flow-init flow-files-submitted="$flow.upload()">
        <div class="well" flow-drop ng-class="dropClass">
            <span class="btn btn-default" flow-btn>@lang('messages.uploader.uploadFiles')</span>
            <span class="btn btn-default" flow-btn flow-directory ng-show="$flow.supportDirectory">@lang('messages.uploader.uploadFolder')</span>
        </div>


        <div>
            <span class="label label-info">@lang('messages.uploader.totalSize')&nbsp;@{{$flow.getSize()}}&nbsp;@lang('messages.uploader.fileWeightUnit')</span>

            <div ng-repeat="file in $flow.files" class="transfer-box">
                @{{file.relativePath}} (@{{file.size}}&nbsp;@lang('messages.uploader.fileWeightUnit'))
                <div class="progress progress-striped" ng-class="{active: file.isUploading()}">
                    <div class="progress-bar" role="progressbar"
                         aria-valuenow="@{{file.progress() * 100}}"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         ng-style="{width: (file.progress() * 100) + '%'}">
                        <span class="sr-only">@{{file.progress()}}% &nbsp;@lang('messages.uploader.uploadComplete')</span>
                    </div>
                </div>

                <a class="btn btn-xs btn-warning" ng-click="file.pause()" ng-show="!file.paused && file.isUploading()">
                    &nbsp;@lang('messages.uploader.pause')
                </a>
                <a class="btn btn-xs btn-warning" ng-click="file.resume()" ng-show="file.paused">
                    &nbsp;@lang('messages.uploader.resume')
                </a>
                <a class="btn btn-xs btn-danger" ng-click="file.cancel()" ng-show="file.isUploading()">
                    &nbsp;@lang('messages.uploader.cancel')
                </a>
                <a class="btn btn-xs btn-info" ng-click="file.retry()" ng-show="file.error">
                    &nbsp;@lang('messages.uploader.retry')
                </a>

            </div>
        </div>
    </div>
@stop

@section('scripts')

{!! HTML::script('assets/javascript/ng-flow.js') !!}

    <script type="text/javascript">

        var koobeApp = angular.module('koobeApp', ['flow', 'notifier'])
            .config(['flowFactoryProvider', function (flowFactoryProvider) {
                flowFactoryProvider.defaults = {
                    target: '{{ URL::action('BookController@flow') }}',
                    permanentErrors: [404, 500, 501],
                    maxChunkRetries: 1,
                    chunkRetryInterval: 5000,
                    simultaneousUploads: 4,
                    headers: function (file, chunk, isTest) {
                        return {
                            'X-XSRF-TOKEN': '{!! $csrfToken !!}'
                        }
                    }
                };
                flowFactoryProvider.on('catchAll', function (event) {
                    //console.log('catchAll', arguments);
                });
                // Can be used with different implementations of Flow.js
                // flowFactoryProvider.factory = fustyFlowFactory;
            }]);
    </script>
@stop
