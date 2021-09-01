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
                                            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $match->when_play)->format('d/m/Y H:i')}}</td>
                                            <td>{{$match->location->formatted_address}}</td>
                                            <td>{{$match->num_players}}</td>
                                            <td class="td-actions text-right">
                                                <a href="{{route('matches.edit', ['id' => $match->id])}}" type="button" rel="tooltip" class="btn btn-primary btn-round">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <form id="deleteForm{{$match->id}}" class="float-right ml-2" method="post" action="{{route('matches.delete', ['id' => $match->id])}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="deleteMatch({{$match->id}})" rel="tooltip" class="btn btn-danger btn-round">
                                                        <i class="material-icons">close</i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">
                                {!! $matches->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function deleteMatch(matchId) {
            const form = document.getElementById('deleteForm' + matchId)
            form.addEventListener('submit', (event) => {
                event.preventDefault()
                Swal.fire({
                    title: '{!! __('general.youSure') !!}',
                    text: "{!! __('general.notAbleRevert') !!}",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{!! __('general.yesDelete') !!}'
                }).then((result) => {
                    if (result.value) {
                        form.submit()
                    }
                })
            })

        }
    </script>
@endpush
