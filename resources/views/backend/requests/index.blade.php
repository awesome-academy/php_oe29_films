@extends('backend.layouts.master')

@section('title'){{ trans('request_list') }}@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/bower_film/backend/css/datatable.css') }}">
@endsection

@section('main__title')
    <h2>{{ trans('request_list') }}</h2>
    <div class="main__title-wrap">
    </div>
@endsection

@section('content')
    <div class="col-12">
        <div class="main__table-wrap">
            <table class="main__table" id="requests-table">
                <thead>
                    <tr>
                        <th>{{ trans('id') }}</th>
                        <th>{{ trans('email') }}</th>
                        <th>{{ trans('content') }}</th>
                        <th>{{ trans('action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('bower_components/bower_film/backend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_film/backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/bower_film/backend/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('build/backend/js/request.js') }}"></script>
@endsection
