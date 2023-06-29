<!doctype html>
<html lang="{{app()->getLocale()}}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Product example · Bootstrap v5.3</title>

        <meta name="theme-color" content="#712cf9">

        <link rel="mask-icon" href="{{ asset('images/icons/touch-icon-vector.svg') }}">
        <link rel="shortcut favicon" href="{{ asset('images/icons/favicon.ico') }}">
        <link rel="shortcut icon" sizes="512x512" href="{{ asset('images/icons/logo512.png') }}">
        <link rel="shortcut icon" sizes="128x128" href="{{ asset('images/icons/logo128.png') }}">
        @include('layouts.includes.meta')

        <link href="{{ mix('css/welcome.css') }}" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-md bg-dark sticky-top border-bottom" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand d-md-none" href="#">
                    <svg class="bi" width="24" height="24">
                        <use xlink:href="#aperture"/>
                    </svg>
                    Aperture
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                        aria-controls="#offcanvas" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end" tabindex="-1" id="#offcanvas" aria-labelledby="#offcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="#offcanvasLabel">Aperture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav flex-grow-1 justify-content-between">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{route('dashboard')}}">
                                    <i class="fa-regular fa-rectangle-list"></i>
                                    {{__('menu.dashboard')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('leaderboard')}}">
                                    <i class="fa-solid fa-ranking-star"></i>
                                    {{__('menu.leaderboard')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('static.about') }}">
                                    <i class="fa-solid fa-file-circle-question"></i>
                                    {{__('menu.about')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('statuses.active') }}">
                                    <i class="fa-solid fa-train"></i>
                                    {{__('menu.active')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    {{__('user.login')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fa-solid fa-user-plus"></i>
                                    {{__('user.register')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-body-tertiary" id="main-intro">
                <div class="col-md-6 p-lg-5 mx-auto my-5">
                    <h1 class="display-3 fw-bold text-primary">Träwelling</h1>
                    <h3 class="fw-normal text-muted mb-3">
                        Hop in - Check in - #NowTräwelling
                    </h3>
                    <div class="d-flex gap-3 justify-content-center lead fw-normal">
                        <a class="icon-link" href="{{route('login')}}">
                            {{__('user.login')}}
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </a>
                        <a class="icon-link" href="#">
                            {{__('user.register')}}
                            <i class="fa-solid fa-user-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="product-device shadow-sm d-none d-md-block"></div>
                <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
            </div>

            <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
                <div class="text-bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5">Fahrtenbuch</h2>
                        <p class="lead">Führe Tagebuch über deine Fahrten im öffentlichen Personenverkehr</p>
                    </div>
                    <div class="bg-body-tertiary shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">Statistiken</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-dark shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
            </div>

            <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">Vernetzen</h2>
                        <p class="lead">Zeige deinen Freunden wohin du reist</p>
                    </div>
                    <div class="bg-dark shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
                <div class="text-bg-primary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5">Punkte sammeln</h2>
                        <p class="lead">Bekomme für jede Fahrt im öffentlichen Verkehr Punkte</p>
                    </div>
                    <div class="bg-body-tertiary shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
            </div>

            <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">Another headline</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-body shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5">Another headline</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-body shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
            </div>

            <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">Another headline</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-body shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
                <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5">Another headline</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-body shadow-sm mx-auto"
                         style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
                </div>
            </div>
        </main>

        <footer class="container py-5">
            <div class="row">
                <div class="col-12 col-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="d-block mb-2" role="img"
                         viewBox="0 0 24 24"><title>Product</title>
                        <circle cx="12" cy="12" r="10"/>
                        <path
                            d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94"/>
                    </svg>
                    <small class="d-block mb-3 text-body-secondary">&copy; 2017–2023</small>
                </div>
                <div class="col-6 col-md">
                    <h5>Features</h5>
                    <ul class="list-unstyled text-small">
                        <li><a class="link-secondary text-decoration-none" href="#">Cool stuff</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Random feature</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Team feature</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Stuff for developers</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Another one</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Last time</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md">
                    <h5>Resources</h5>
                    <ul class="list-unstyled text-small">
                        <li><a class="link-secondary text-decoration-none" href="#">Resource name</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Resource</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Another resource</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Final resource</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md">
                    <h5>Resources</h5>
                    <ul class="list-unstyled text-small">
                        <li><a class="link-secondary text-decoration-none" href="#">Business</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Education</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Government</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Gaming</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md">
                    <h5>About</h5>
                    <ul class="list-unstyled text-small">
                        <li><a class="link-secondary text-decoration-none" href="#">Team</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Locations</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Privacy</a></li>
                        <li><a class="link-secondary text-decoration-none" href="#">Terms</a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </body>
</html>
