@extends('layouts.app', ['activePage' => 'matches.chat', 'titlePage' => __('matches.title')])

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
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.update', ['id' => $match->id])}}">
                        {{__('matches.update')}}
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.enrolled', ['id' => $match->id])}}">
                        {{__('matches.enrolled')}}
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
                            <h4 class="card-title text-center"><b>{{strtoupper(__('matches.chat'))}}</b></h4>
                        </div>
                        <div class="card-body">
                            @if(count($messages) > 0)
                                @foreach($messages as $message)
                                    @if($message->owner_id == auth()->user()->id)
                                        <div class="col-10 col-md-5 mr-auto text-left rounded p-3 m-2" style="background-color: #EEEEEE;">
                                            <h4 class="text-right float-right">
                                                <small style="font-size:62%; margin-bottom: 0">
                                                        <span class="badge ml-2 py-1 px-2" style="background-color: #ff7e62!important">
                                                            <?= date("d/m/Y H:i", strtotime($message->created_at)); ?>
                                                        </span>
                                                </small>
                                            </h4>
                                            <h4 class="mb-2" style="color: #ff7e62!important">
                                                <?= $message->owner->name; ?>
                                            </h4>
                                            <p class="mb-2 text-secondary"><?= nl2br($message->text); ?></p>
                                        </div>
                                    @else
                                        <div class="col-10 col-md-5 ml-auto text-left rounded p-3 m-2" style="background-color: #EEEEEE;">
                                            <h4 class="text-right float-right">
                                                <small style="font-size:62%; margin-bottom: 0">
                                                        <span class="badge ml-2 py-1 px-2 text-right" style="background-color: #ff7e62!important">
                                                            <?= date("d/m/Y H:i", strtotime($message->created_at)); ?>
                                                        </span>
                                                </small>
                                            </h4>
                                            <h4 class="mb-2" style="color: #ff7e62!important">
                                                <?= $message->owner->name; ?>
                                            </h4>
                                            <p class="mb-2 text-secondary"><?= nl2br($message->text); ?></p>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="d-flex justify-content-center">
                                    {!! $messages->links() !!}
                                </div>
                            @else
                                No hay mensajes
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')

@endpush

