@php
    $siteInfo = DB::table('site_information')->first();
    $wheretoStudies = DB::table('where_to_studies')
        ->where('is_done', 1)
        ->orderBy('priority')
        ->take(6)
        ->get();
    $logoPath = optional($siteInfo)->logo;
    $logoUrl = $logoPath ? (filter_var($logoPath, FILTER_VALIDATE_URL) ? $logoPath : asset($logoPath)) : asset('images/logo.png');
    $socialLinks = collect([
        ['icon' => 'la-facebook', 'url' => optional($siteInfo)->facebook ?? optional($siteInfo)->facebook_link ?? optional($siteInfo)->facebook_url],
        ['icon' => 'la-twitter', 'url' => optional($siteInfo)->twitter ?? optional($siteInfo)->twitter_link ?? optional($siteInfo)->twitter_url],
        ['icon' => 'la-instagram', 'url' => optional($siteInfo)->instagram ?? optional($siteInfo)->instagram_link ?? optional($siteInfo)->instagram_url],
        ['icon' => 'la-linkedin', 'url' => optional($siteInfo)->linkedin ?? optional($siteInfo)->linkedin_link ?? optional($siteInfo)->linkedin_url],
        ['icon' => 'la-youtube', 'url' => optional($siteInfo)->youtube ?? optional($siteInfo)->youtube_link ?? optional($siteInfo)->youtube_url],
    ])->filter(fn ($link) => !empty($link['url']));
@endphp

    <!--======================================
        SUBSCRIBER AREA (Global)
    ======================================-->
    <section class="subscriber-area pt-60px pb-60px bg-gray">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <div class="section-heading mb-0">
                        <span class="badge bg-warning bg-opacity-25 text-warning text-uppercase mb-2">Newsletter</span>
                        <h2 class="section__title mb-2 text-dark">Subscribe to newsletter</h2>
                        <p class="section__desc text-muted mb-0">Stay connected to get new course update </p>
                    </div>
                </div>
                <div class="col-lg-5 ms-auto">
                    <form method="post" action="#" class="subscriber-form">
                        @csrf
                        <div class="input-group border rounded-pill overflow-hidden shadow-sm">
                            <input
                                type="email"
                                name="email"
                                class="form-control border-0 ps-4"
                                placeholder="Enter email address"
                                required
                            />
                            <button class="btn theme-btn rounded-pill px-4" type="button">
                                Subscribe <i class="la la-arrow-right icon ms-1"></i>
                            </button>
                        </div>
                        <p class="fs-14 mt-2 mb-0 text-muted">
                            <i class="la la-lock me-1"></i>Your information is safe with us! Unsubscribe anytime.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!--======================================
        END SUBSCRIBER AREA
    ======================================-->

    <section class="footer-area pt-100px">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 responsive-column-half">
            <div class="footer-item">
              <a href="{{ route('home.index') }}" class="d-inline-block mb-3">
                <img
                  src="{{ $logoUrl }}"
                  alt="footer logo"
                  class="footer__logo"
                  style="max-height:80px;width:auto;"
                />
              </a>
                                <p class="generic-list-item pt-4 text-muted">
                                    {{ strip_tags(optional($siteInfo)->description) }}
                                </p>
                                @if($socialLinks->isNotEmpty())
                                    <h3 class="fs-20 font-weight-semi-bold pt-4 pb-2">We are on</h3>
                                    <ul class="social-icons social-icons-styled">
                                        @foreach($socialLinks as $link)
                                            <li class="me-1">
                                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener">
                                                    <i class="la {{ $link['icon'] ?? $link['platform'] }}"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <!-- end footer-item -->
                        </div>
                        <!-- end col-lg-3 -->
                        <div class="col-lg-3 responsive-column-half">
                            <div class="footer-item">
                                <h3 class="fs-20 font-weight-semi-bold">Quick Links</h3>
                                <span class="section-divider section--divider"></span>
                                <ul class="generic-list-item">
                                    <li><a href="{{ route('who_we_are') }}">About Us</a></li>
                                    <li><a href="{{ route('consultation.step1') }}">Book Consultation</a></li>
                                    <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                                    <li><a href="{{ route('all.blogs') }}">Blogs</a></li>
                                    <li><a href="{{ route('free-courses') }}">Free courses</a></li>
                                </ul>
                            </div>
                            <!-- end footer-item -->
                        </div>
                        <!-- end col-lg-3 -->
                        <div class="col-lg-3 responsive-column-half">
                            <div class="footer-item">
                                <h3 class="fs-20 font-weight-semi-bold">Universities</h3>
                                <span class="section-divider section--divider"></span>
                                <ul class="generic-list-item">
                                    @forelse($wheretoStudies as $study)
                                        <li><a href="{{ route('where.to.study', $study->slug) }}">{{ $study->name }}</a></li>
                                    @empty
                                        <li><span class="text-muted">Partners coming soon</span></li>
                                    @endforelse
                                </ul>
                            </div>
                            <!-- end footer-item -->
                        </div>
                        <!-- end col-lg-3 -->
                        <div class="col-lg-3 responsive-column-half">
                            <div class="footer-item">
                                <h3 class="fs-20 font-weight-semi-bold">Contact Information</h3>
                                <span class="section-divider section--divider"></span>
                                <ul class="generic-list-item">
                                    <li><a href="tel:{{ optional($siteInfo)->mobile1 }}">{{ optional($siteInfo)->mobile1 }}</a></li>
                                    <li><a href="mailto:{{ optional($siteInfo)->email1 }}">{{ optional($siteInfo)->email1 }}</a></li>
                                    <li>{{ optional($siteInfo)->address }}</li>
                                </ul>
                            </div>
                            <!-- end footer-item -->
                        </div>
                        <!-- end col-lg-3 -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
                <div class="section-block"></div>
                <div class="copyright-content py-4">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <p class="copy-desc">
                                    © Copyright 2025 Horizons Unlimited. Develop by
                                    <a href="https://blacktechcorp.com/">Blacktech</a>
                                </p>
                            </div>
                            <!-- end col-lg-6 -->
                            @php 
                                $pages = DB::table('pages')->get();
                            @endphp
                            <div class="col-lg-6">
                                <div class="d-flex flex-wrap align-items-center justify-content-end">
                                    <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
                                        @foreach($pages as $page)
                                        <li class="me-3">
                                            <a href="{{ route('page.show', $page->slug) }}">{{$page->title}}</a>
                                        </li>
                                        @endforeach
                                        <!--<li class="me-3">-->
                                        <!--    <a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a>-->
                                        <!--</li>-->
                                        
                                        <!--<li class="me-3">-->
                                        <!--    <a href="{{ route('page.show', 'sitemap') }}">Sitemap</a>-->
                                        <!--</li>-->
                                        
                                    </ul>
                                    <!-- test -->
                                    <div class="select-container select-container-sm">
                                        <!-- <select class="select-container-select">
                    <option value="1">English</option>
                    <option value="2">Deutsch</option>
                    <option value="3">Español</option>
                    <option value="4">Français</option>
                    <option value="5">Bahasa Indonesia</option>
                    <option value="6">Bangla</option>
                    <option value="7">日本語</option>
                    <option value="8">한국어</option>
                    <option value="9">Nederlands</option>
                    <option value="10">Polski</option>
                    <option value="11">Português</option>
                    <option value="12">Română</option>
                    <option value="13">Русский</option>
                    <option value="14">ภาษาไทย</option>
                    <option value="15">Türkçe</option>
                    <option value="16">中文(简体)</option>
                    <option value="17">中文(繁體)</option>
                    <option value="17">Hindi</option>
                  </select> -->
                                    </div>
                                </div>
                            </div>
                            <!-- end col-lg-6 -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end container -->
                </div>
                <!-- end copyright-content -->
    </section>
    <!-- end footer-area -->
    <!-- ================================
          END FOOTER AREA
================================= -->

    <!-- start scroll top -->
    <div id="scroll-top">
        <i class="la la-arrow-up" title="Go top"></i>
    </div>
    <!-- end scroll top -->

   

    <a href="https://wa.me/12024597853" class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <i class="la la-whatsapp"></i>
    </a>

    <style>
        .floating-whatsapp {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: #25d366;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            font-size: 28px;
            z-index: 999;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .floating-whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 24px rgba(0,0,0,0.25);
            color: #fff;
        }
    </style>

    <!-- template js files -->
    <script src="{{asset('frontend/assets/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/isotope.js')}}"></script>
    <script src="{{asset('frontend/assets/js/waypoint.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/jquery.counterup.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/fancybox.js')}}"></script>
    <script src="{{asset('frontend/assets/js/datedropper.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/emojionearea.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/select2.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/tooltipster.bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/jquery.lazy.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/main.js')}}"></script>
  </body>
</html>
