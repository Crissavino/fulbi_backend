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
    <meta name="description" content="Bootstrap App Landing Template">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="author" content="Themefisher">
    <meta name="generator" content="Themefisher Small Apps Template v1.0">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon.ico')}}" />
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
<section class="section gradient-banner">
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
        <div class="row align-items-center">
            <div class="col-md-6 order-2 order-md-1 text-center text-md-left">
                <h1 class="text-white font-weight-bold mb-4">Fulbito App</h1>
                <p class="text-white mb-5">{!! __('landing.matches-all-one-place') !!}</p>
                <a href="#android" id="downloadButton" class="btn btn-main-md">{!! __('landing.download') !!}</a>
            </div>
            <div class="col-md-6 text-center order-1 order-md-2">
                <img class="img-fluid" src="landing/images/mobile-2.png" alt="screenshot" style="max-width: 70% !important;">
            </div>
        </div>
    </div>
</section>
<!--====  End of Hero Section  ====-->

<!--==================================
=            Feature Grid            =
===================================-->
<section class="feature section pt-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 ml-auto justify-content-center">
                <!-- Feature Mockup -->
                <div class="image-content" data-aos="fade-right">
                    <img class="img-fluid" src="landing/images/create-edit-match.png" alt="iphone">
                </div>
            </div>
            <div class="col-lg-6 mr-auto align-self-center">
                <div class="feature-content">
                    <!-- Feature Title -->
                    <h2>{!! __('landing.easy-create-edit-matches') !!}</h2>
                    <!-- Feature Description -->
                    <p class="desc">{!! __('landing.easy-create-edit-matches-desc') !!}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="feature section pt-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mr-auto align-self-center">
                <div class="feature-content">
                    <!-- Feature Title -->
                    <h2>{!! __('landing.search-players') !!}</h2>
                    <!-- Feature Description -->
                    <p class="desc">{!! __('landing.search-players-desc') !!}</p>
                </div>
            </div>
            <div class="col-lg-6 ml-auto justify-content-center">
                <!-- Feature Mockup -->
                <div class="image-content" data-aos="fade-left">
                    <img class="img-fluid" src="landing/images/players-invite.png" alt="iphone">
                </div>
            </div>
        </div>
    </div>
</section>

{{--<section class="feature section pt-0">--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-6 ml-auto align-self-center">--}}
{{--                <div class="feature-content">--}}
{{--                    <!-- Feature Title -->--}}
{{--                    <h2>Increase your productivity with <a--}}
{{--                            href="https://themefisher.com/products/small-apps-free-app-landing-page-template/">Small Apps</a></h2>--}}
{{--                    <!-- Feature Description -->--}}
{{--                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et--}}
{{--                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex--}}
{{--                        ea commodo consequat.</p>--}}
{{--                </div>--}}
{{--                <!-- Testimonial Quote -->--}}
{{--                <div class="testimonial">--}}
{{--                    <p>--}}
{{--                        "InVision is a window into everything that's being designed at Twitter. It gets all of our best work in one--}}
{{--                        place."--}}
{{--                    </p>--}}
{{--                    <ul class="list-inline meta">--}}
{{--                        <li class="list-inline-item">--}}
{{--                            <img src="landing/images/testimonial/feature-testimonial-thumb.jpg" alt="">--}}
{{--                        </li>--}}
{{--                        <li class="list-inline-item">Jonathon Andrew , Themefisher.com</li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-6 mr-auto justify-content-center">--}}
{{--                <!-- Feature mockup -->--}}
{{--                <div class="image-content" data-aos="fade-left">--}}
{{--                    <img class="img-fluid" src="landing/images/feature/feature-new-02.jpg" alt="ipad">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--====  End of Feature Grid  ====-->

<!--==============================
=            Services            =
===============================-->

<section class="section pt-0 position-relative pull-top">
    <div class="service-thumb centerElement" data-aos="fade-right">
        <img class="img-fluid infoChatParticipantsImage" src="landing/images/info-chat-participants.png" alt="iphone-ipad">
    </div>
    <div class="container">
        <div class="rounded shadow p-5 bg-white">
            <div class="row">
                <div class="m-auto text-center">
                    <div class="feature-content mb-3 mb-md-5">
                        <h2>{!! __('landing.every-created-match') !!}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mt-5 mt-md-0 text-center">
                    <img class="img" src="landing/images/info.png" alt="iphone-ipad" style="width: 65px;">
                    <h3 class="mt-4 text-capitalize h5 ">{!! __('landing.match-info') !!}</h3>
                    <p class="regular text-muted">{!! __('landing.match-participants-desc') !!}</p>
                </div>
                <div class="col-lg-4 col-md-6 mt-5 mt-md-0 text-center">
                    <img class="img" src="landing/images/participants.png" alt="iphone-ipad" style="width: 65px;">
                    <h3 class="mt-4 text-capitalize h5 ">{!! __('landing.match-participants') !!}</h3>
                    <p class="regular text-muted">{!! __('landing.match-participants-desc') !!}</p>
                </div>
                <div class="col-lg-4 col-md-12 mt-5 mt-lg-0 text-center">
                    <img class="img" src="landing/images/chat.png" alt="iphone-ipad" style="width: 65px;">
                    <h3 class="mt-4 text-capitalize h5 ">{!! __('landing.match-chat') !!}</h3>
                    <p class="regular text-muted">{!! __('landing.match-chat-desc') !!}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{--<section class="service section bg-gray">--}}
{{--    <div class="container-fluid p-0">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-12">--}}
{{--                <div class="section-title">--}}
{{--                    <h2>Match</h2>--}}
{{--                    <p>Check the information of the match, who are the players participating and chat with all all of them in the chat room available here!</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="row no-gutters">--}}
{{--            <div class="col-lg-6 align-self-center">--}}
{{--                <!-- Feature Image -->--}}
{{--                <div class="service-thumb left" data-aos="fade-right">--}}
{{--                    <img class="img-fluid" src="landing/images/info-chat-participants.png" alt="iphone-ipad">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-5 mr-auto align-self-center">--}}
{{--                <div class="service-box">--}}
{{--                    <div class="row align-items-center">--}}
{{--                        <div class="col-md-6 col-xs-12">--}}
{{--                            <!-- Service 01 -->--}}
{{--                            <div class="service-item">--}}
{{--                                <!-- Icon -->--}}
{{--                                <i class="ti-bookmark"></i>--}}
{{--                                <!-- Heading -->--}}
{{--                                <h3>Easy Prototyping</h3>--}}
{{--                                <!-- Description -->--}}
{{--                                <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur aliquet quam id dui</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-xs-12">--}}
{{--                            <!-- Service 01 -->--}}
{{--                            <div class="service-item">--}}
{{--                                <!-- Icon -->--}}
{{--                                <i class="ti-pulse"></i>--}}
{{--                                <!-- Heading -->--}}
{{--                                <h3>Sensor Bridge</h3>--}}
{{--                                <!-- Description -->--}}
{{--                                <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur aliquet quam id dui</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-xs-12">--}}
{{--                            <!-- Service 01 -->--}}
{{--                            <div class="service-item">--}}
{{--                                <!-- Icon -->--}}
{{--                                <i class="ti-bar-chart"></i>--}}
{{--                                <!-- Heading -->--}}
{{--                                <h3>Strategist</h3>--}}
{{--                                <!-- Description -->--}}
{{--                                <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur aliquet quam id dui</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 col-xs-12">--}}
{{--                            <!-- Service 01 -->--}}
{{--                            <div class="service-item">--}}
{{--                                <!-- Icon -->--}}
{{--                                <i class="ti-panel"></i>--}}
{{--                                <!-- Heading -->--}}
{{--                                <h3>Art Direction</h3>--}}
{{--                                <!-- Description -->--}}
{{--                                <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur aliquet quam id dui</p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--====  End of Services  ====-->


<!--=================================
=            Video Promo            =
==================================-->
{{--<section class="video-promo section bg-1">--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-12">--}}
{{--                <div class="content-block">--}}
{{--                    <!-- Heading -->--}}
{{--                    <h2>Watch Our Promo Video</h2>--}}
{{--                    <!-- Promotional Speech -->--}}
{{--                    <p>Vivamus suscipit tortor eget felis porttitor volutpat. Curabitur arcu erat, accumsan id imperdiet et,--}}
{{--                        porttitor at sem. Vivamus </p>--}}
{{--                    <!-- Popup Video -->--}}
{{--                    <a data-fancybox href="https://www.youtube.com/watch?v=jrkvirglgaQ">--}}
{{--                        <i class="ti-control-play video"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--====  End of Video Promo  ====-->

<!--=================================
=            Testimonial            =
==================================-->
{{--<section class="section testimonial" id="testimonial">--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-12">--}}
{{--                <!-- Testimonial Slider -->--}}
{{--                <div class="testimonial-slider owl-carousel owl-theme">--}}
{{--                    <!-- Testimonial 01 -->--}}
{{--                    <div class="item">--}}
{{--                        <div class="block shadow">--}}
{{--                            <!-- Speech -->--}}
{{--                            <p>--}}
{{--                                Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Donec sollicitudin molestie malesuada.--}}
{{--                                Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus. Lorem ipsum dolor--}}
{{--                                sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Quisque velit nisi,--}}
{{--                                pretium ut lacinia in, elementum id enim.--}}
{{--                            </p>--}}
{{--                            <!-- Person Thumb -->--}}
{{--                            <div class="image">--}}
{{--                                <img src="landing/images/testimonial/feature-testimonial-thumb.jpg" alt="image">--}}
{{--                            </div>--}}
{{--                            <!-- Name and Company -->--}}
{{--                            <cite>Abraham Linkon , Themefisher.com</cite>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!-- Testimonial 01 -->--}}
{{--                    <div class="item">--}}
{{--                        <div class="block shadow">--}}
{{--                            <!-- Speech -->--}}
{{--                            <p>--}}
{{--                                Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Donec sollicitudin molestie malesuada.--}}
{{--                                Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus. Lorem ipsum dolor--}}
{{--                                sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Quisque velit nisi,--}}
{{--                                pretium ut lacinia in, elementum id enim.--}}
{{--                            </p>--}}
{{--                            <!-- Person Thumb -->--}}
{{--                            <div class="image">--}}
{{--                                <img src="landing/images/testimonial/feature-testimonial-thumb.jpg" alt="image">--}}
{{--                            </div>--}}
{{--                            <!-- Name and Company -->--}}
{{--                            <cite>Abraham Linkon , Themefisher.com</cite>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!-- Testimonial 01 -->--}}
{{--                    <div class="item">--}}
{{--                        <div class="block shadow">--}}
{{--                            <!-- Speech -->--}}
{{--                            <p>--}}
{{--                                Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Donec sollicitudin molestie malesuada.--}}
{{--                                Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus. Lorem ipsum dolor--}}
{{--                                sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Quisque velit nisi,--}}
{{--                                pretium ut lacinia in, elementum id enim.--}}
{{--                            </p>--}}
{{--                            <!-- Person Thumb -->--}}
{{--                            <div class="image">--}}
{{--                                <img src="landing/images/testimonial/feature-testimonial-thumb.jpg" alt="image">--}}
{{--                            </div>--}}
{{--                            <!-- Name and Company -->--}}
{{--                            <cite>Abraham Linkon , Themefisher.com</cite>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!-- Testimonial 01 -->--}}
{{--                    <div class="item">--}}
{{--                        <div class="block shadow">--}}
{{--                            <!-- Speech -->--}}
{{--                            <p>--}}
{{--                                Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Donec sollicitudin molestie malesuada.--}}
{{--                                Donec sollicitudin molestie malesuada. Pellentesque in ipsum id orci porta dapibus. Lorem ipsum dolor--}}
{{--                                sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Quisque velit nisi,--}}
{{--                                pretium ut lacinia in, elementum id enim.--}}
{{--                            </p>--}}
{{--                            <!-- Person Thumb -->--}}
{{--                            <div class="image">--}}
{{--                                <img src="landing/images/testimonial/feature-testimonial-thumb.jpg" alt="image">--}}
{{--                            </div>--}}
{{--                            <!-- Name and Company -->--}}
{{--                            <cite>Abraham Linkon , Themefisher.com</cite>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--====  End of Testimonial  ====-->

<section class="call-to-action-app section bg-green-gradient">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="mb-5">{!! __('landing.time-to-play') !!}</b></h2>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="" class="btn btn-rounded-icon">
                            <i class="ti-apple"></i>
                            {!! __('landing.iphone') !!}
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="" class="btn btn-rounded-icon">
                            <i class="ti-android"></i>
                            {!! __('landing.android') !!}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!--============================
=            Footer            =
=============================-->
<footer>
{{--    <div class="footer-main">--}}
{{--        <div class="container">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-12 m-md-auto align-self-center">--}}
{{--                    <div class="block">--}}
{{--                        <a href="index.html"><img src="landing/images/logo-alt.png" alt="footer-logo"></a>--}}
{{--                        <!-- Social Site Icons -->--}}
{{--                        <ul class="social-icon list-inline">--}}
{{--                            <li class="list-inline-item">--}}
{{--                                <a href="https://www.facebook.com/themefisher"><i class="ti-facebook"></i></a>--}}
{{--                            </li>--}}
{{--                            <li class="list-inline-item">--}}
{{--                                <a href="https://twitter.com/themefisher"><i class="ti-twitter"></i></a>--}}
{{--                            </li>--}}
{{--                            <li class="list-inline-item">--}}
{{--                                <a href="https://www.instagram.com/themefisher/"><i class="ti-instagram"></i></a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-2 col-md-3 col-6 mt-5 mt-lg-0">--}}
{{--                    <div class="block-2">--}}
{{--                        <!-- heading -->--}}
{{--                        <h6>Product</h6>--}}
{{--                        <!-- links -->--}}
{{--                        <ul>--}}
{{--                            <li><a href="team.html">Teams</a></li>--}}
{{--                            <li><a href="blog.html">Blogs</a></li>--}}
{{--                            <li><a href="FAQ.html">FAQs</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-2 col-md-3 col-6 mt-5 mt-lg-0">--}}
{{--                    <div class="block-2">--}}
{{--                        <!-- heading -->--}}
{{--                        <h6>Resources</h6>--}}
{{--                        <!-- links -->--}}
{{--                        <ul>--}}
{{--                            <li><a href="sign-up.html">Singup</a></li>--}}
{{--                            <li><a href="sign-in.html">Login</a></li>--}}
{{--                            <li><a href="blog.html">Blog</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-2 col-md-3 col-6 mt-5 mt-lg-0">--}}
{{--                    <div class="block-2">--}}
{{--                        <!-- heading -->--}}
{{--                        <h6>Company</h6>--}}
{{--                        <!-- links -->--}}
{{--                        <ul>--}}
{{--                            <li><a href="career.html">Career</a></li>--}}
{{--                            <li><a href="contact.html">Contact</a></li>--}}
{{--                            <li><a href="team.html">Investor</a></li>--}}
{{--                            <li><a href="privacy.html">Terms</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-2 col-md-3 col-6 mt-5 mt-lg-0">--}}
{{--                    <div class="block-2">--}}
{{--                        <!-- heading -->--}}
{{--                        <h6>Company</h6>--}}
{{--                        <!-- links -->--}}
{{--                        <ul>--}}
{{--                            <li><a href="about.html">About</a></li>--}}
{{--                            <li><a href="contact.html">Contact</a></li>--}}
{{--                            <li><a href="team.html">Team</a></li>--}}
{{--                            <li><a href="privacy-policy.html">Privacy Policy</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="text-center bg-dark py-4">
        <div id="selectLang">
            <form
                action="@if(request()->path() === '/') {{route('changeLang')}} @elseif(request()->path() === 'home') {{route('changeLangHome')}} @else {{route('changeLang')}} @endif"
                method="POST" id="changeLang">
                @csrf
                <select class="btn btn-rounded-icon" name="lang" style="width: 200px">
                    <option @if(Illuminate\Support\Facades\App::getLocale() === 'en') selected @endif value="en">English</option>
                    <option @if(Illuminate\Support\Facades\App::getLocale() === 'es') selected @endif value="es">Español</option>
                </select>
            </form>
        </div>
        <small class="text-secondary">Copyright &copy; <script>document.write(new Date().getFullYear())</script>. Designed &amp; Developed by jackCode</small>
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
        setUrlDownloadButton();
        changeLang();
    };

    function getMobileOperatingSystem() {
        let userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Windows Phone must come first because its UA also contains "Android"
        if (/windows phone/i.test(userAgent)) {
            return "Windows Phone";
        }

        if (/android/i.test(userAgent)) {
            return "Android";
        }

        // iOS detection from: http://stackoverflow.com/a/9039885/177710
        if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            return "iOS";
        }

        return "unknown";
    }

    function changeLang() {
        let changeLang = document.getElementById('changeLang');
        changeLang.addEventListener('change', () => changeLang.submit())
    }

    function setUrlDownloadButton() {
        const platform = getMobileOperatingSystem();

        let downloadButton = document.getElementById('downloadButton');
        if (platform === 'iOS') {
            downloadButton.setAttribute('href', '#iosUrl');
        }else if (platform === 'Android') {
            downloadButton.setAttribute('href', '#androidUrl');
        } else {
            downloadButton.setAttribute('href', '#');
        }
    }
</script>
</body>

</html>
