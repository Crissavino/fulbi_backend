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
                            <h4 class="card-title ">{{__('general.players')}}</h4>
                            <p class="card-category">{{__('general.yourPlayers')}}</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class=" text-primary">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Nickname</th>
                                    <th class="text-right">Actions</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($players as $player)
                                        <tr>
                                            <td>{{$player->id}}</td>
                                            <td>{{$player->user->name}}</td>
                                            <td>{{$player->user->nickname}}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" class="btn btn-primary btn-round">
                                                    <i class="material-icons">visibility</i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
