@extends('layouts.app', ['activePage' => 'matches.create', 'titlePage' => __('matches.title')])

@section('content')
<div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <a class="text-white btn btn-primary btn-lg" href="{{route('matches.all')}}">
                        {{__('matches.back')}}
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
                            <h4 class="card-title text-center"><b>{{strtoupper(__('matches.create'))}}</b></h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="form" action="{{route('matches.store')}}">
                                @csrf
                                @METHOD('POST')
                                <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                        <label for="wherePlay" class="label-control">{{__('matches.wherePlay')}}</label>
                                        <input type="text" class="form-control" id="wherePlay" name="location" required>
                                    </div>
                                    <div class="col-12 col-md-6 mt-3 mt-sm-0">
                                        <label for="whenPlay" class="label-control">{{__('matches.whenPlay')}}</label>
                                        <input type="text" class="form-control datetimepicker" id="whenPlay" name="when_play" required>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                        <label for="whenPlay" class="label-control">{{__('matches.genreType')}}</label>

                                        <div class="row m-auto">
                                            @foreach ($genres as $genre)
                                                <div class="col-3 form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" required type="radio" name="genre_id" id="genre" value="{{$genre->id}}" >
                                                        {{__($genre->name_key)}}
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="whenPlay" class="label-control">{{__('matches.matchType')}}</label>

                                        <div class="row m-auto">
                                            @foreach ($types as $type)
                                                <div class="col-3 form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" required type="radio" name="type_id" id="type" value="{{$type->id}}" >
                                                        {{__($type->name_key)}}
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                        <label for="cost" class="label-control">{{__('matches.matchCost')}}</label>
                                        <input type="number" min="0" class="form-control" id="cost" name="cost" step=".01" required>
                                    </div>
                                    <div class="col-12 col-md-6 mt-3 mt-sm-0">
                                        <label for="numPlayers" class="label-control">{{__('matches.matchNumPlayers')}}</label>
                                        <input type="number" min="1" max="40" class="form-control" id="numPlayers" name="num_players" required>
                                    </div>
                                </div>

                                <input name="userId" class="d-none" type="text" value="{{auth()->user()->id}}">

                                <div class="row">
                                    <button type="submit" class="col-6 mx-auto my-5 text-white btn btn-primary btn-lg">{{__('general.send')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{$apiKey}}&libraries=places&callback=initialize">
    </script>

    <script>
        $('.datetimepicker').datetimepicker({
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            },
            format:'DD/MM/YYYY HH:mm',
            minDate: new Date()
        });

        const AK = '{{$apiKey}}'
        function initialize() {
            const options = {
                fields: ["formatted_address", "geometry", "name", "address_components", "place_id"],
            };
            const input = document.getElementById('wherePlay')
            const autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.addListener("place_changed", () => {

                const place = autocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                let data = {
                    'lat': place.geometry.location.lat().toString(),
                    'lng': place.geometry.location.lng().toString(),
                    'formatted_address': place.formatted_address,
                    'place_id': place.place_id,
                }
                if (place.address_components.length > 0) {
                    place.address_components.forEach( (val) => {
                        switch (val.types[0]) {
                            case "locality":
                                data.city = val.long_name
                                break;
                            case "administrative_area_level_1":
                                data.province = val.long_name
                                data.province_code = val.short_name
                                break;
                            case "country":
                                data.country = val.long_name
                                data.country_code = val.short_name
                                break;
                            default:
                                break;
                        }

                    })
                }
                if (!document.getElementById('locationData')) {
                    let textArea = document.createElement('TEXTAREA')
                    textArea.setAttribute('name', 'locationData')
                    textArea.setAttribute('id', 'locationData')
                    textArea.classList.add('d-none')
                    textArea.value = '';
                    textArea.value = JSON.stringify(data);
                    document.getElementById('form').append(textArea)
                } else {
                    let textArea = document.getElementById('locationData');
                    textArea.value = '';
                    textArea.value = JSON.stringify(data);
                    document.getElementById('form').append(textArea)
                }

            });
        }

    </script>
{{--    <script src="{{asset('js/createMatch.js')}}"></script>--}}
@endpush

