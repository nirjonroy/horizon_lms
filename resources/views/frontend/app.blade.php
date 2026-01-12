<!DOCTYPE html>
<html lang="en">
    @include('frontend.head')
  <body>
      
<!-- start cssload-loader -->
    <!--<div class="preloader">-->
    <!--    <div class="loader">-->
    <!--        <svg class="spinner" viewBox="0 0 50 50">-->
    <!--      <circle-->
    <!--        class="path"-->
    <!--        cx="25"-->
    <!--        cy="25"-->
    <!--        r="20"-->
    <!--        fill="none"-->
    <!--        stroke-width="5"-->
    <!--      ></circle>-->
    <!--    </svg>-->
    <!--    </div>-->
    <!--</div>-->
    <!-- end cssload-loader -->


<div class="content hidden">


    @include('frontend.header')

    @yield('content')
</div>    
 
    @include('frontend.footer')
    
    
    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
 
