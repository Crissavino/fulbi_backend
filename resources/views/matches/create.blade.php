@extends('layouts.app', ['activePage' => 'matches.create', 'titlePage' => __('matches.title')])

<style>
    .mapContainer {
        position: relative;
        height: 600px;
        margin: 100px 100px 100px 20px;
        /*display: none;*/
        display: block;
    }

    .close-map-button {
        left: 10px;
    }

    .success-map-button {
        margin-left: 20px !important;
    }

    .content {
        /*display: block;*/
        display: none;
    }

    .map {
        position: absolute;
        top: 0;
        bottom: 0;
        height: 100%;
        width: 100%;
        overflow: visible;
        box-shadow: 0 4px 14px -4px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
    }

    .description {
        border: 1px solid #00bcd4 !important;
        border-radius: 12px !important;
        padding-left: 10px;
    }

</style>

@section('content')
    <div class="mapContainer">
        <button class="text-white btn btn-primary btn-lg close-map-button"
                onclick="closeMap()">{{__('matches.back')}}</button>

        <button class="text-white btn btn-success btn-lg success-map-button"
                onclick="selectLocation()">Select location</button>

        <div id="map" class="map"></div>
    </div>

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
                                        <input type="text" class="d-none" id="locationData" name="locationData">
                                    </div>
                                    <div class="col-12 col-md-6 mt-3 mt-sm-0">
                                        <label for="whenPlay" class="label-control">{{__('matches.whenPlay')}}</label>
                                        <input type="text" class="form-control datetimepicker" id="whenPlay" name="when_play" required autocomplete="off">
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12 col-md-6">
                                        <label for="whenPlay" class="label-control">{{__('matches.genreType')}}</label>

                                        <div class="row m-auto">
                                            @foreach ($genres as $genre)
                                                <div class="col-3 form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" required type="radio"
                                                               name="genre_id" id="genre{{$genre->id}}" value="{{$genre->id}}">
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
                                        <label for="type" class="label-control">{{__('matches.matchType')}}</label>

                                        <div class="row m-auto">
                                            @foreach ($types as $type)
                                                <div class="col-3 form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" required type="radio"
                                                               name="type_id" id="type{{$type->id}}" value="{{$type->id}}">
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
                                    <div class="col-2 col-md-1">
                                        <select class="form-control currency-select" name="currency_id" id="">
                                            @foreach ($currencies as $currency)
                                                <option value="{{$currency->id}}">{{$currency->symbol}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="cost" class="label-control">{{__('matches.matchCost')}}</label>
                                        <input type="number" min="0" class="form-control cost" id="cost" name="cost"
                                               step=".1" required>
                                    </div>
                                    <div>
                                        <label for="isFreeMatch">Free</label>
                                        <input class="form-control" type="checkbox" name="is_free_match" id="isFreeMatch" value="false" onchange="checkFreeMatch(this)">
                                    </div>
                                    <div class="col-10 offset-1 col-md-4 mt-3 mt-sm-0">
                                        <label for="numPlayers"
                                               class="label-control">{{__('matches.matchNumPlayers')}}</label>
                                        <input type="number" min="1" max="40" class="form-control" id="numPlayers"
                                               name="num_players" required>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <textarea style="padding-left: 10px;" class="form-control border-info description" name="description" id="description" cols="30" rows="10" placeholder="Description (optional)"></textarea>
                                    </div>
                                </div>

                                <input name="userId" class="d-none" type="text" value="{{auth()->user()->id}}">

                                <div class="row">
                                    <button type="submit"
                                            class="col-6 mx-auto my-5 text-white btn btn-primary btn-lg">{{__('general.create')}}</button>
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
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js"></script>
    <script
        src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>

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
            format: 'DD/MM/YYYY HH:mm',
            minDate: new Date()
        });
    </script>

    <script>

        let locationData;
        let textToInput;

        window.onload = () => {
            initMapBox();
            document.querySelector('.mapContainer').style.display = "none"
            document.querySelector('.content').style.display = "block"
        }

        function checkFreeMatch(element) {
            if (element.checked) {
                let currencySelect = document.querySelector('.currency-select')
                currencySelect.options[0].setAttribute('selected', 'true')
                currencySelect.options[1].setAttribute('disabled', 'true')
                currencySelect.options[2].setAttribute('disabled', 'true')
                let cost = document.querySelector('.cost')
                cost.value = 0;
                cost.setAttribute('readonly', 'true')
            } else {
                let currencySelect = document.querySelector('.currency-select')
                currencySelect.options[0].setAttribute('selected', 'true')
                currencySelect.options[1].removeAttribute('disabled')
                currencySelect.options[2].removeAttribute('disabled')
                let cost = document.querySelector('.cost')
                cost.removeAttribute('readonly')
            }
        }

        function initMapBox() {
            mapboxgl.accessToken = `{{$mapBoxApiKey}}`;
            let center = [`{{$userLng}}`, `{{$userLat}}`];
            if (locationData !== undefined) {
                center = [`${locationData.lng}`, `${locationData.lat}`]
            }
            const map = new mapboxgl.Map({
                container: 'map', // container ID
                style: 'mapbox://styles/mapbox/streets-v11', // style URL
                center: center, // starting position [lng, lat]
                zoom: 13 // starting zoom
            });

            const geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                mapboxgl: mapboxgl
            });

            map.addControl(geocoder);

            const marker = new mapboxgl.Marker({
                draggable: true
            })
                .setLngLat([`{{$userLng}}`, `{{$userLat}}`])
                .addTo(map);

            async function onDragEnd() {
                const lngLat = marker.getLngLat();
                let response = await searchByLngLat(lngLat.lng, lngLat.lat);
                locationData = formatMapBoxResponse(response);
            }

            marker.on('dragend', onDragEnd);

            // After the map style has loaded on the page,
            // add a source layer and default styling for a single point
            map.on('load', () => {
                // Listen for the `result` event from the Geocoder // `result` event is triggered when a user makes a selection
                //  Add a marker at the result's coordinates
                geocoder.on('result', async ({result}) => {
                    geocoder.clear();
                    marker.setLngLat([result.center[0], result.center[1]]).addTo(map);
                    let response = await searchByLngLat(result.center[0], result.center[1]);
                    locationData = formatMapBoxResponse(response);
                });
            });
        }

        function formatMapBoxResponse(response) {
            let place = response.features[0];
            const latitude = place.center[1];
            const longitude = place.center[0];
            const context = Array.from(place.context);

            const city = context.filter(context => context.id.includes('place'))[0].text
            const province = context.filter(context => context.id.includes('region'))[0].text
            const country = context.filter(context => context.id.includes('country'))[0].text

            return {
                'lat': latitude,
                'lng': longitude,
                'formatted_address': place.text,
                'place_name': place.place_name,
                'place_id': null,
                'city': city,
                'province': province,
                'province_code': null,
                'country': country,
                'country_code': null,
                'is_by_lat_lng': true,
            };
        }

        async function searchByLngLat(lng, lat) {
            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},%20${lat}.json?access_token={{$mapBoxApiKey}}&autocomplete=true`;
            return await fetch(url)
                .then(response => response.json())
                .then(data => data);
        }

        let wherePlayInput = document.getElementById('wherePlay');
        wherePlayInput.addEventListener('focusin', () => {
            let mapContainer = document.querySelector('.mapContainer');
            let contentDiv = document.querySelector('.content');
            mapContainer.style.display = "block"
            contentDiv.style.display = "none"
            let mapboxGL = document.querySelector('.mapboxgl-canvas');
            mapboxGL.style.width = "1300px"
            mapboxGL.style.height = "600px"
            mapboxGL.setAttribute('width', '2600');
            mapboxGL.setAttribute('height', '1200');
            wherePlayInput.blur()
        })

        function closeMap() {
            let mapContainer = document.querySelector('.mapContainer');
            let contentDiv = document.querySelector('.content');
            mapContainer.style.display = "none"
            contentDiv.style.display = "block"
        }

        function selectLocation() {
            let mapContainer = document.querySelector('.mapContainer');
            let contentDiv = document.querySelector('.content');
            mapContainer.style.display = "none"
            contentDiv.style.display = "block"

            let locationDataInput = document.getElementById('locationData');
            locationDataInput.value = JSON.stringify(locationData);
            textToInput = `${Math.round(locationData.lat * 100) / 100} - ${Math.round(locationData.lng * 100) / 100}`
            let wherePlayInput = document.getElementById('wherePlay');
            wherePlayInput.value = textToInput;
        }
    </script>

@endpush

