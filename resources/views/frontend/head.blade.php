<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <meta name="author" content="Imad_hajj"> -->
  <meta name="google-site-verification" content="wAw4hUVvSKTBeG8hb1WH9Gl37n2wS_BtK5vxVHzhVMg" />
  <!-- head -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token()}}">
  @php
                $siteInfo = DB::table('site_information')->first();
            @endphp
  <link rel="icon" type="image/png" sizes="32x32" href="{{asset($siteInfo->logo)}}" />
  @yield('seos')
  <title>@yield('title', 'Horizons Unlimited')</title>
    
        <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    
  <link rel="icon" type="image/png" sizes="32x32" href="{{asset($siteInfo->logo)}}" />

    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('frontend/assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/select2.min.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/animate.min.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/owl.carousel.min.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/owl.theme.default.min.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/fancybox.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/tooltipster.bundle.css')}}" />
    <link rel="stylesheet" href="{{asset('frontend/assets/css/style.css')}}" />

{!!\App\Models\Script::value('google')!!}
{!!\App\Models\Script::value('facebook')!!}
  </head>
