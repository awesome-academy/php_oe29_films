<header class="header">
    <div class="header__wrap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header__content">
                        <!-- header logo -->
                        <a href="{{ route('frontend.home') }}" class="header__logo">
                            <img src="{{ asset('bower_components/bower_film/img/LOGO.png')}}" alt="">
                        </a>
                        <!-- end header logo -->
                        <!-- header nav -->
                        <ul class="header__nav">
                            <!-- dropdown -->
                            <li class="header__nav-item">
                                <a class="dropdown-toggle header__nav-link" href="{{ route('frontend.home') }}">{{ trans('home') }}</a>
                            </li>
                            <!-- end dropdown -->
                            <!-- dropdown -->
                            <li class="header__nav-item">
                                <a class="dropdown-toggle header__nav-link" href="#" role="button" id="dropdownMenuCatalog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ trans('type') }}</a>

                                <ul class="dropdown-menu header__dropdown-menu" aria-labelledby="dropdownMenuCatalog">
                                @foreach ($mainMenu['types_movie'] as $type)
                                    <li><a href="{{ route('frontend.catalog', ['type', $type['slug']]) }}">{{ trans($type['title']) }}</a></li>
                                @endforeach
                                </ul>
                            </li>
                            <!-- end dropdown -->
                            <!-- dropdown -->
                            <li class="header__nav-item">
                                <a class="dropdown-toggle header__nav-link" href="{{ route('frontend.catalog', ['genre', $mainMenu['genre'][config('config.default_id_genre_movie')]['order']]) }}">{{ trans($mainMenu['genre'][config('config.default_id_genre_movie')]['name']) }}</a>
                            </li>
                            <!-- end dropdown -->
                            <!-- dropdown -->
                            <li class="header__nav-item">
                                <a class="dropdown-toggle header__nav-link" href="{{ route('frontend.catalog', ['genre', $mainMenu['genre'][config('config.default_id_genre_tvserie')]['order']]) }}" >{{ trans($mainMenu['genre'][config('config.default_id_genre_tvserie')]['name']) }}</a>
                            </li>
                            <!-- end dropdown -->
                            <li class="header__nav-item">
                                <a class="dropdown-toggle header__nav-link" href="#" role="button" id="dropdownMenuCatalog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ trans('country') }}</a>
                                <ul class="dropdown-menu header__dropdown-menu" aria-labelledby="dropdownMenuCatalog">
                                @foreach ($mainMenu['country'] as $country)
                                    <li>
                                        <a href="{{ route('frontend.catalog', ['country', $country['order']]) }}">{{ trans($country['name']) }}</a>
                                    </li>
                                @endforeach
                                </ul>
                            </li>
                            <!-- end dropdown -->
                        </ul>
                        <!-- end header nav -->
                        <!-- header auth -->
                        <div class="header__auth">
                            <button class="header__search-btn" type="button">
                                <i class="icon ion-ios-search"></i>
                            </button>

                            @if (auth()->check())
                                <a href="{{ route('logout') }}" class="header__sign-in">
                                    <i class="icon ion-ios-log-in"></i>
                                    <span>{{ trans('sign_out') }}</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="header__sign-in">
                                    <i class="icon ion-ios-log-in"></i>
                                    <span>{{ trans('sign_in') }}</span>
                                </a>
                            @endif

                            <div class="dropdown header__lang">
                                <a class="dropdown-toggle header__nav-link" href="#" role="button" id="dropdownMenuLang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Session::has('language') ? Session::get('language') : trans('EN') }}</a>

                                <ul class="dropdown-menu header__dropdown-menu" aria-labelledby="dropdownMenuLang">
                                    <li><a href="{{ route('frontend.language',['en']) }}">{{ trans('EN') }}</a></li>
                                    <li><a href="{{ route('frontend.language',['vi']) }}">{{ trans('VI') }}</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- end header auth -->
                        <!-- header menu btn -->
                        <button class="header__btn" type="button">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                        <!-- end header menu btn -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- header search -->
    <form action="{{ route('frontend.searchMovie') }}" class="header__search" method="get">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header__search-content">
                        <input type="text" name="search" placeholder="{{ trans('search_key') }}">
                        <button type="submit">{{ trans('search') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- end header search -->
</header>
