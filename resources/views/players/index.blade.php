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
                                                <button data-toggle="modal" data-target="#profileModal{{$player->id}}" type="button" rel="tooltip" class="btn btn-primary btn-round text-white">
                                                    <i class="material-icons">visibility</i>
                                                </button>

                                                <div class="modal fade" id="profileModal{{$player->id}}" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true" style="margin-top: 100px">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="col-md-12 m-auto">
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
                                                                    <h2 class="card-title text-center font-weight-bold">{{$player->user->name}}</h2>
                                                                    <h4 class="card-category text-center font-weight-bold">{{'@'.$player->user->nickname}}</h4>
                                                                </div>
                                                                <div class="card-body m-auto text-center">
                                                                    @if(!is_null($player->user->profile_image))
                                                                        <img src="{{$player->user->profile_image}}" alt="profile picture" style="width: 150px; height: 150px;border-radius: 50%;">
                                                                    @endif
                                                                    <h3 class="font-weight-bold">{{__('Usually plays in')}}</h3>
                                                                    <h4>{{$player->location->formatted_address}}</h4>
                                                                    <h3 class="font-weight-bold">{{__('Positions')}}</h3>
                                                                    @foreach($player->positions as $position)
                                                                        <h4>{{__($position->name_key)}}</h4>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
