<?php
?>
<!--
THEME: Small Apps | Bootstrap App Landing Template
VERSION: 1.0.0
AUTHOR: Themefisher

HOMEPAGE: https://themefisher.com/products/small-apps-free-app-landing-page-template/
DEMO: https://demo.themefisher.com/small-apps/
GITHUB: https://github.com/themefisher/Small-Apps-Bootstrap-App-Landing-Template

Get HUGO Version : https://themefisher.com/products/small-apps-hugo-app-landing-theme/

WEBSITE: https://themefisher.com
TWITTER: https://twitter.com/themefisher
FACEBOOK: https://www.facebook.com/themefisher
-->

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <title>{!! __('landing.title') !!}</title>

    <!-- Mobile Specific Metas
    ================================================== -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="{!! __('landing.matches-all-one-place') !!}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="jackCode">

    <meta property="og:title" content="{!! __('landing.title') !!}">
    <meta property="og:description" content="{!! __('landing.matches-all-one-place') !!}">
    <meta property="og:url" content="{{route('home')}}">
    <meta property="og:image" content="{{asset('landing/images/image-for-link.png')}}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon.ico')}}"/>
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('favicon.ico')}}">

    <!-- PLUGINS CSS STYLE -->
    <link rel="stylesheet" href="{{asset('landing/plugins/bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/plugins/themify-icons/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('landing/plugins/slick/slick.css')}}">
    <link rel="stylesheet" href="{{asset('landing/plugins/slick/slick-theme.css')}}">
    <link rel="stylesheet" href="{{asset('landing/plugins/fancybox/jquery.fancybox.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/plugins/aos/aos.css')}}">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="{{asset('landing/css/style.css')}}">

</head>

<body class="body-wrapper" data-spy="scroll" data-target=".privacy-nav">

<!--====================================
=            Hero Section            =
=====================================-->
<section class="section gradient-banner mb-5">
    <div class="shapes-container">
        <div class="shape" data-aos="fade-down-left" data-aos-duration="1500" data-aos-delay="100"></div>
        <div class="shape" data-aos="fade-down" data-aos-duration="1000" data-aos-delay="100"></div>
        <div class="shape" data-aos="fade-up-right" data-aos-duration="1000" data-aos-delay="200"></div>
        <div class="shape" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"></div>
        <div class="shape" data-aos="fade-down-left" data-aos-duration="1000" data-aos-delay="100"></div>
        <div class="shape" data-aos="fade-down-left" data-aos-duration="1000" data-aos-delay="100"></div>
        <div class="shape" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="300"></div>
        <div class="shape" data-aos="fade-down-right" data-aos-duration="500" data-aos-delay="200"></div>
        <div class="shape" data-aos="fade-down-right" data-aos-duration="500" data-aos-delay="100"></div>
        <div class="shape" data-aos="zoom-out" data-aos-duration="2000" data-aos-delay="500"></div>
        <div class="shape" data-aos="fade-up-right" data-aos-duration="500" data-aos-delay="200"></div>
        <div class="shape" data-aos="fade-down-left" data-aos-duration="500" data-aos-delay="100"></div>
        <div class="shape" data-aos="fade-up" data-aos-duration="500" data-aos-delay="0"></div>
        <div class="shape" data-aos="fade-down" data-aos-duration="500" data-aos-delay="0"></div>
        <div class="shape" data-aos="fade-up-right" data-aos-duration="500" data-aos-delay="100"></div>
        <div class="shape" data-aos="fade-down-left" data-aos-duration="500" data-aos-delay="0"></div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <!-- Page Title -->
                <h1 class="font-weight-bold text-white">{!! __('privacy.title') !!}</h1>
            </div>
        </div>
    </div>
</section>
<!--====  End of Hero Section  ====-->

<!--====================================
=            Privacy Policy            =
=====================================-->

<section class="privacy section pt-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="block">
                    <p>
                        {!! __('privacy.p1') !!}
                    </p>
                    <p>
                        {!! __('privacy.p2') !!}
                    </p>
                    <p>
                        {!! __('privacy.p3') !!}
                    </p>
                    <p>
                        {!! __('privacy.p4') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p5') !!}</strong></p>
                    <p>
                        {!! __('privacy.p5') !!}
                    </p>
                    <div>
                        <p>
                            {!! __('privacy.p6') !!}

                        </p>
                        <p>
                            {!! __('privacy.p7') !!}
                        </p>
                        <ul>
                            <li>
                                <a href="https://www.google.com/policies/privacy/" target="_blank" rel="noopener noreferrer">Google Play Services</a>
                            </li>
                            <li>
                                <a href="https://firebase.google.com/policies/analytics" target="_blank" rel="noopener noreferrer">Google Analytics for Firebase</a>
                            </li>
                            <li>
                                <a href="https://firebase.google.com/support/privacy/" target="_blank" rel="noopener noreferrer">Firebase Crashlytics</a>
                            </li>
                            <li>
                                <a href="https://www.mapbox.com/legal/privacy" target="_blank" rel="noopener noreferrer">Mapbox</a>
                            </li>
                        </ul>
                    </div>
                    <p><strong>{!! __('privacy.title.p8') !!}</strong></p>
                    <p>
                        {!! __('privacy.p8') !!}
                    </p>
                    <p><strong>Cookies</strong></p>
                    <p>
                        {!! __('privacy.p9') !!}
                    </p>
                    <p>
                        {!! __('privacy.p10') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p11') !!}</strong></p>
                    <p>
                        {!! __('privacy.p11') !!}
                    </p>
                    <ul>
                        <li>{!! __('privacy.p11.li.1') !!}</li>
                        <li>{!! __('privacy.p11.li.2') !!};</li>
                        <li>{!! __('privacy.p11.li.3') !!}</li>
                        <li>{!! __('privacy.p11.li.4') !!}</li>
                    </ul>
                    <p>
                        {!! __('privacy.p12') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p13') !!}</strong></p>
                    <p>
                        {!! __('privacy.p13') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p14') !!}</strong></p>
                    <p>
                        {!! __('privacy.p14') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p15') !!}</strong></p>
                    <p>
                        {!! __('privacy.p15') !!}
                    </p>
                    <p><strong>{!! __('privacy.title.p16') !!}</strong></p>
                    <p>
                        {!! __('privacy.p16') !!}
                    </p>
                    <p>{!! __('privacy.p17') !!}</p>
                    <p><strong>{!! __('privacy.title.p18') !!}</strong></p>
                    <p>
                        {!! __('privacy.p18') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====  End of Privacy Policy  ====-->

<!--============================
=            Footer            =
=============================-->
<footer>
    <div class="footer-main">
        <div class="container">
            <div class="row">
                <div class="col-md-12 m-md-auto align-self-center text-center">
                    <div class="d-block">
                        <a href="{{route('home')}}">
                            <img src="{{asset('/landing/images/footer_logo.png')}}" alt="footer-logo" width="100"
                                 style="margin-left: 20px;">
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6 mt-5 mt-lg-0 m-auto">
                    <div class="block-2">
                        <!-- heading -->
                    {{--                        <h6>Company</h6>--}}
                    <!-- links -->
                        <ul style="display: flex; flex-direction: row">
                            <li class="m-3"><a href="{{route('terms')}}">{!! __('terms.title') !!}</a></li>
                            <li class="m-3"><a href="{{route('privacy')}}">{!! __('privacy.title') !!}</a></li>
                        </ul>
                    </div>

                    <div id="selectLang">
                        <form
                            action="{{route('changeLangPrivacy')}}"
                            method="POST" id="changeLang">
                            @csrf
                            <select id="selectLangSelect" class="btn btn-rounded-icon" name="lang" style="width: 200px">
                                <option @if(Illuminate\Support\Facades\App::getLocale() === 'en') selected
                                        @endif value="en">English
                                </option>
                                <option @if(Illuminate\Support\Facades\App::getLocale() === 'es') selected
                                        @endif value="es">Español
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center bg-dark py-4">
        <small class="text-secondary">Copyright &copy;
            <script>document.write(new Date().getFullYear())</script>
            . Designed &amp; Developed by jackCode</small>
    </div>
</footer>

<!-- To Top -->
<div class="scroll-top-to">
    <i class="ti-angle-up"></i>
</div>

<!-- JAVASCRIPTS -->
<script src="{{ asset('landing/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('landing/plugins/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('landing/plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('landing/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
<script src="{{ asset('landing/plugins/syotimer/jquery.syotimer.min.js') }}"></script>
<script src="{{ asset('landing/plugins/aos/aos.js') }}"></script>

<script src="{{ asset('landing/js/script.js') }}"></script>

<script>


    window.onload = () => {
        changeLang();
    };

    function changeLang() {
        let changeLang = document.getElementById('changeLang');
        changeLang.addEventListener('change', () => changeLang.submit())
    }

</script>
</body>

</html>
