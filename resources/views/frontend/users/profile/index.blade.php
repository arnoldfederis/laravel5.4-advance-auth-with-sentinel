@extends('frontend.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Profile</div>

                    <div class="panel-body">
                        <h3>{{ $user->full_name }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection