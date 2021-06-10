@extends('layouts.app', ['activePage' => 'matches.index', 'titlePage' => __('matches.title')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <a class="text-white btn btn-primary btn-lg float-right" href="{{route('matches.add')}}">
                        {{__('matches.create')}}
                    </a>
                </div>
            </div>
        </div>
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
                            <h4 class="card-title ">{{__('matches.title')}}</h4>
                            <p class="card-category">{{__('matches.yourMatches')}}</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class=" text-primary">
                                    <th>ID</th>
                                    <th>Match Date</th>
                                    <th>Place</th>
                                    <th>Players needed</th>
                                    <th class="text-right">Actions</th>
                                    </thead>
                                    <tbody>
                                    @foreach ($matches as $match)
                                        <tr>
                                            <td>{{$match->id}}</td>
                                            <td>{{\Carbon\Carbon::parse($match->when_play)->format('d/m/Y H:i')}}</td>
                                            <td>{{$match->location->formatted_address}}</td>
                                            <td>{{$match->num_players}}</td>
                                            <td class="td-actions text-right">
                                                <button type="button" rel="tooltip" class="btn btn-success btn-round">
                                                    <i class="material-icons">edit</i>
                                                </button>
                                                <button type="button" rel="tooltip" class="btn btn-danger btn-round">
                                                    <i class="material-icons">close</i>
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
