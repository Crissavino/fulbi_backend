@extends('layouts.app', ['activePage' => 'players.index', 'titlePage' => __('matches.title')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session()->get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title ">{{$player->user->name}}</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="form" action="{{route('players.update', ['id' => $player->id])}}">
                                @csrf
                                @METHOD('POST')
                                <div class="row mt-5">
                                    <div class="col-12 col-sm-4">
                                        <label for="fullName" class="label-control">Full Name</label>
                                        <input type="text" class="form-control" id="fullName" value="{{$player->user->name}}" name="name" required>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="nickName" class="label-control">Nickname</label>
                                        <input type="text" class="form-control" id="nickName" value="{{$player->user->nickname}}" name="nickname" required>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="email" class="label-control">Email</label>
                                        <input type="text" class="form-control" id="email" value="{{$player->user->email}}" name="email" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <button type="submit" class="col-6 mx-auto my-5 text-white btn btn-primary btn-lg">{{__('general.update')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
