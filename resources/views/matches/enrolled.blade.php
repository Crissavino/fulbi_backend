@extends('layouts.app', ['activePage' => 'matches.enrolled', 'titlePage' => __('matches.title')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-md-4">
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.all')}}">
                        {{__('matches.back')}}
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.chat', ['id' => $match->id])}}">
                        {{__('matches.chat')}}
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.update', ['id' => $match->id])}}">
                        {{__('matches.update')}}
                    </a>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if(count($errors) !== 0)
                        <div class="alert alert-danger" role="alert">

                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title text-center"><b>{{strtoupper(__('matches.enrolled'))}}</b></h4>
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
@push('js')

@endpush

