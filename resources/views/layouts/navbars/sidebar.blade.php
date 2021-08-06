<div class="sidebar" data-color="green" data-background-color="white"
     data-image="{{ asset('material') }}/img/sidebar-1.jpg">
    <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
    <div class="logo">
        <a href="{{ route('home') }}" class="simple-text logo-normal">
            Fulbito
        </a>
    </div>

    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="material-icons">dashboard</i>
                    <p>{{ __('Dashboard') }}</p>
                </a>
            </li>
            <li class="nav-item{{ ($activePage == 'matches.index' || $activePage == 'matches.create' || $activePage == 'matches.update') ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('matches.all') }}">
                    <i class="material-icons">sports_soccer</i>
                    <p>{{ __('sidebar.matches') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'players.index' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('players.all') }}">
                    <i class="material-icons">people</i>
                    <p>{{ __('sidebar.players') }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
