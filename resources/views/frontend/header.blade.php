<header class="header-menu-area bg-white">
  @php
            $siteInfo = DB::table('site_information')->first();
            $exploreMenuCategories = $exploreMenuCategories ?? collect();
            $buildCourseUrl = function (array $filters = []) {
                $filters = array_filter($filters, fn ($value) => filled($value));

                if (isset($filters['child'])) {
                    return route('courses.child.show', [
                        'category' => $filters['category'],
                        'subcategory' => $filters['subcategory'],
                        'childCategory' => $filters['child'],
                    ]);
                }

                if (isset($filters['subcategory'])) {
                    return route('courses.subcategory.show', [
                        'category' => $filters['category'],
                        'subcategory' => $filters['subcategory'],
                    ]);
                }

                if (isset($filters['category'])) {
                    return route('courses.category.show', [
                        'category' => $filters['category'],
                    ]);
                }

                return route('premium-courses');
            };
          @endphp
          
   <div class="header-top pe-150px ps-150px border-bottom border-bottom-gray py-1">
      <div class="container-fluid">
         <div class="row align-items-center">
            <div class="col-lg-6">
               <div class="header-widget">
                  <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
                     <li class="d-flex align-items-center pe-3 me-3 border-right border-right-gray">
                        <i class="la la-phone me-1"></i
                           ><a href="tel:{{$siteInfo->mobile1}}"> {{$siteInfo->mobile1}}</a>
                     </li>
                     <li class="d-flex align-items-center">
                        <i class="la la-envelope-o me-1"></i
                           ><a href="mailto:{{$siteInfo->email1}}"> {{$siteInfo->email1}}</a>
                     </li>
                  </ul>
               </div>
               <!-- end header-widget -->
            </div>
            <!-- end col-lg-6 -->
            <div class="col-lg-6">
               <div
                  class="header-widget d-flex flex-wrap align-items-center justify-content-end"
                  >
                  <div class="theme-picker d-flex align-items-center">
                     <button
                        class="theme-picker-btn dark-mode-btn"
                        title="Dark mode"
                        >
                        <svg
                           id="moon"
                           viewBox="0 0 24 24"
                           stroke-width="1.5"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           >
                           <path
                              d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"
                              ></path>
                        </svg>
                     </button>
                     <button
                        class="theme-picker-btn light-mode-btn"
                        title="Light mode"
                        >
                        <svg
                           id="sun"
                           viewBox="0 0 24 24"
                           stroke-width="1.5"
                           stroke-linecap="round"
                           stroke-linejoin="round"
                           >
                           <circle cx="12" cy="12" r="5"></circle>
                           <line x1="12" y1="1" x2="12" y2="3"></line>
                           <line x1="12" y1="21" x2="12" y2="23"></line>
                           <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                           <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                           <line x1="1" y1="12" x2="3" y2="12"></line>
                           <line x1="21" y1="12" x2="23" y2="12"></line>
                           <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                           <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                     </button>
                  </div>
                  <ul
                     class="generic-list-item d-flex flex-wrap align-items-center fs-14 border-left border-left-gray ps-3 ms-3"
                     >
                     @guest
                     <li
                        class="d-flex align-items-center pe-3 me-3 border-right border-right-gray"
                        >
                        <i class="la la-sign-in me-1"></i
                           ><a href="{{ route('login') }}"> Login</a>
                     </li>
                     @else
                      <li
                          class="d-flex align-items-center pe-3 me-3 border-right border-right-gray"
                          >
                          <i class="la la-user me-1"></i
                             ><a href="{{ url('/user-dashboard') }}"> Dashboard</a>
                        </li>
                        <li class="d-flex align-items-center">
                        <i class="la la-user me-1"></i
                           ><form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                    Logout
                  </button>
                </form>
                     </li>
                     @endguest
                     <!-- <li class="d-flex align-items-center">
                        <i class="la la-user me-1"></i
                           ><a href="{{ route('register') }}"> Register</a>
                     </li> -->
                  </ul>
               </div>
               <!-- end header-widget -->
            </div>
            <!-- end col-lg-6 -->
         </div>
         <!-- end row -->
      </div>
      <!-- end container-fluid -->
   </div>
   <!-- end header-top -->
   <div class="header-menu-content pe-150px ps-150px bg-white">
      <div class="container-fluid">
         <div class="main-menu-content">
            <a href="#" class="down-button"><i class="la la-angle-down"></i></a>
            <div class="row align-items-center">
               <div class="col-lg-2">
                  <div class="logo-box">
                     <a href="{{ route('home.index') }}" class="logo"><img src="{{ asset($siteInfo->logo) }}" alt="logo" class="imag-fluid" style="width: 80px;"
                        /></a>
                     <div class="user-btn-action">
                        <a href="{{ route('cart.view') }}" class="icon-element icon-element-sm shadow-sm me-2 d-inline-flex d-lg-none" data-toggle="tooltip" data-placement="top" title="Cart">
                           <i class="la la-shopping-cart"></i>
                        </a>
                        <a href="{{ route('consultation.step1') }}" class="icon-element icon-element-sm shadow-sm me-2 d-inline-flex d-lg-none" data-toggle="tooltip" data-placement="top" title="Book Consultancy">
                           <i class="la la-user-plus"></i>
                        </a>
                        <div class="search-menu-toggle icon-element icon-element-sm shadow-sm me-2" data-toggle="tooltip" data-placement="top" title="Search">
                           <i class="la la-search"></i>
                        </div>
                        <div class="off-canvas-menu-toggle cat-menu-toggle icon-element icon-element-sm shadow-sm me-2" data-toggle="tooltip" data-placement="top" title="Category menu">
                           <i class="la la-th-large"></i>
                        </div>
                        <div class="off-canvas-menu-toggle main-menu-toggle icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="top" title="Main menu">
                           <i class="la la-bars"></i>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- end col-lg-2 -->
               <div class="col-lg-10">
                  <div class="menu-wrapper">
                     <div class="menu-category">
                        <ul>
                           <li>
                              <a href="{{route('premium-courses')}}">Explore <i class="la la-angle-down fs-12"></i></a>
                              <ul class="cat-dropdown-menu">
                                 <li>
                                    <a href="{{ route('bundle-programs') }}">Unlimited &amp; Bundle Programs</a>
                                 </li>
                                 @forelse($exploreMenuCategories as $category)
                                    @php
                                       $hasSubcategories = $category->subcategories->isNotEmpty();
                                       $categoryUrl = $buildCourseUrl(['category' => $category->slug]);
                                    @endphp
                                    <li class="{{ $hasSubcategories ? 'has-children' : '' }}">
                                       <a href="{{ $categoryUrl }}">
                                          {{ $category->name }}
                                          @if($hasSubcategories)
                                             <i class="la la-angle-right"></i>
                                          @endif
                                       </a>
                                       @if($hasSubcategories)
                                          <ul class="sub-menu">
                                             <li>
                                                <a href="{{ $categoryUrl }}" class="fw-semibold">
                                                   All {{ $category->name }}
                                                </a>
                                             </li>
                                             @foreach($category->subcategories as $subcategory)
                                                @php
                                                   $hasChildCategories = $subcategory->childCategories->isNotEmpty();
                                                   $subcategoryUrl = $buildCourseUrl([
                                                       'category' => $category->slug,
                                                       'subcategory' => $subcategory->slug,
                                                   ]);
                                                @endphp
                                                <li class="{{ $hasChildCategories ? 'has-children' : '' }}">
                                                   <a href="{{ $subcategoryUrl }}">
                                                      {{ $subcategory->name }}
                                                      @if($hasChildCategories)
                                                         <i class="la la-angle-right"></i>
                                                      @endif
                                                   </a>
                                                   @if($hasChildCategories)
                                                      <ul class="sub-menu child-menu">
                                                         <li>
                                                            <a href="{{ $subcategoryUrl }}" class="fw-semibold">
                                                               All {{ $subcategory->name }}
                                                            </a>
                                                         </li>
                                                         @foreach($subcategory->childCategories as $childCategory)
                                                            @php
                                                               $childUrl = $buildCourseUrl([
                                                                   'category' => $category->slug,
                                                                   'subcategory' => $subcategory->slug,
                                                                   'child' => $childCategory->slug,
                                                               ]);
                                                            @endphp
                                                            <li><a href="{{ $childUrl }}">{{ $childCategory->name }}</a></li>
                                                         @endforeach
                                                      </ul>
                                                   @endif
                                                </li>
                                             @endforeach
                                          </ul>
                                       @endif
                                    </li>
                                 @empty
                                    <li class="px-4 py-3 text-muted">{{ __('No categories available yet.') }}</li>
                                 @endforelse
                              </ul>
                           </li>
                        </ul>
                     </div>
                     <!-- end menu-category -->
                     <form method="GET" action="{{ route('search') }}" class="search-form">
                         <div class="form-group mb-0">
                            <input class="form-control form--control ps-3" type="text" name="search" value="{{ request('search', '') }}" placeholder="Search for anything" />
                           <button type="submit" class="search-icon" aria-label="Search">
                              <i class="la la-search"></i>
                           </button>
                         </div>
                      </form>
                     <nav class="main-menu">
                        <ul>
                            <li>
                               <a href="{{ route('price.plan') }}">Price and Plan </a>
                            </li>
                           <!-- <li>
                              <a href="#">Horizons Business </a>
                           </li> -->
                           <li class="mega-menu-has">
                              <a href="javascript:void(0)">
                                 Universities <i class="la la-angle-down fs-12"></i>
                              </a>
                              @php
                                  $wheretoStudies = DB::table('where_to_studies')
                                      ->where('is_done', 1)
                                      ->orderBy('name')
                                      ->get();
                                  $studyCount = $wheretoStudies->count();
                                  $columnTarget = $studyCount >= 15 ? 3 : ($studyCount > 6 ? 2 : 1);
                                  $perColumn = $studyCount ? (int) ceil($studyCount / $columnTarget) : 0;
                                  $studyColumns = $perColumn ? $wheretoStudies->chunk($perColumn) : collect();
                              @endphp
                              <ul class="dropdown-menu-item mega-menu mega-menu-universities">
                                 @forelse($studyColumns as $column)
                                    <li>
                                       <ul class="mega-menu-list list-unstyled mb-0">
                                          @foreach ($column as $study)
                                             <li class="university-menu-item">
                                                <hr class="university-divider divider-top" aria-hidden="true" />
                                                <a href="{{ route('where.to.study', $study->slug) }}">
                                                   {{ $study->name }}
                                                </a>
                                                <hr class="university-divider divider-bottom" aria-hidden="true" />
                                             </li>
                                          @endforeach
                                       </ul>
                                    </li>
                                 @empty
                                    <li class="px-3 py-2 text-muted">
                                       {{ __('Partner universities coming soon.') }}
                                    </li>
                                 @endforelse
                              </ul>
                           </li>
                        </ul>
                        <!-- end ul -->
                     </nav>
                     <!-- end main-menu -->
                    <div class="shop-cart me-4">
                        @php
                            $cartItems = $headerCartItems ?? collect();
                            $cartTotal = $headerCartSubtotal ?? 0;
                            $cartOldTotal = $headerCartOldSubtotal ?? null;
                        @endphp
                        <ul>
                           <li>
                              <button type="button" class="shop-cart-btn d-flex align-items-center bg-transparent border-0">
                                 <i class="la la-shopping-cart"></i>
                                 <span class="product-count">{{ $headerCartCount ?? 0 }}</span>
                              </button>
                              <ul class="cart-dropdown-menu">
                                 @forelse($cartItems as $item)
                                    @php
                                        $itemImage = $item['image'] ?? null;
                                        if ($itemImage && !\Illuminate\Support\Str::startsWith($itemImage, ['http://', 'https://'])) {
                                            $itemImage = asset($itemImage);
                                        }
                                        $itemImage = $itemImage ?: asset('frontend/assets/images/img-loading.png');
                                        $itemQuantity = $item['quantity'] ?? 1;
                                        $itemPrice = (float) ($item['price'] ?? 0);
                                    @endphp
                                    <li class="media media-card">
                                        <a href="{{ isset($item['slug']) ? route('course.show', $item['slug']) : '#' }}" class="media-img">
                                            <img
                                               src="{{ $itemImage }}"
                                               alt="{{ $item['title'] ?? 'Cart image' }}"
                                            />
                                        </a>
                                        <div class="media-body">
                                           <h5>
                                              <a href="{{ isset($item['slug']) ? route('course.show', $item['slug']) : '#' }}">
                                                {{ \Illuminate\Support\Str::limit($item['title'] ?? 'Course', 70) }}
                                              </a>
                                           </h5>
                                           <span class="d-block lh-18 py-1">
                                              {{ $itemQuantity }} × {{ $item['instructor'] ?? 'Horizons Faculty' }}
                                           </span>
                                           <p class="text-black font-weight-semi-bold lh-18 mb-0">
                                              ${{ number_format($itemPrice, 2) }}
                                              @if(!empty($item['old_price']))
                                                 <span class="before-price fs-14">${{ number_format((float) $item['old_price'], 2) }}</span>
                                              @endif
                                           </p>
                                        </div>
                                        @auth
                                            <a href="{{ route('cart.remove', $item['id']) }}" class="icon-element icon-element-xs shadow-sm ms-2" title="Remove">
                                                <i class="la la-times"></i>
                                            </a>
                                        @endauth
                                    </li>
                                 @empty
                                    <li class="p-4 text-center text-muted">
                                        {{ __('Your cart is empty.') }}
                                    </li>
                                 @endforelse
                                 <li class="media media-card">
                                    <div class="media-body fs-16 d-flex flex-column">
                                       <p class="text-black font-weight-semi-bold lh-18 mb-0">
                                          Total: <span class="cart-total">${{ number_format($cartTotal, 2) }}</span>
                                          @if($cartOldTotal && $cartOldTotal > $cartTotal)
                                             <span class="before-price fs-14">${{ number_format($cartOldTotal, 2) }}</span>
                                          @endif
                                       </p>
                                    </div>
                                 </li>
                                 <li>
                                    <a
                                       href="{{ route('cart.view') }}"
                                       class="btn theme-btn w-100"
                                       >{{ __('Go to cart') }}
                                    <i class="la la-arrow-right icon ms-1"></i
                                       ></a>
                                 </li>
                              </ul>
                           </li>
                        </ul>
                     </div>
                     <!-- end shop-cart -->
                     <div class="nav-right-button">
                        <a href="{{ route('consultation.step1') }}" class="btn theme-btn d-none d-lg-inline-block"><i class="la la-user-plus me-1"></i> Book Consultancy</a
                           >
                     </div>
                     <!-- end nav-right-button -->
                  </div>
                  <!-- end menu-wrapper -->
               </div>
               <!-- end col-lg-10 -->
            </div>
            <!-- end row -->
         </div>
      </div>
      <!-- end container-fluid -->
   </div>
   <!-- end header-menu-content -->
   <div class="off-canvas-menu custom-scrollbar-styled main-off-canvas-menu">
      <div
         class="off-canvas-menu-close main-menu-close icon-element icon-element-sm shadow-sm"
         data-toggle="tooltip"
         data-placement="left"
         title="Close menu"
         >
         <i class="la la-times"></i>
      </div>
      <!-- end off-canvas-menu-close -->
      <ul class="generic-list-item off-canvas-menu-list pt-90px">
         <li>
            <a href="{{ route('home.index') }}">{{ __('Home') }}</a>
         </li>
         <li class="{{ $exploreMenuCategories->isNotEmpty() ? 'has-children' : '' }}">
            <a href="{{ route('premium-courses') }}">
               Explore
               @if($exploreMenuCategories->isNotEmpty())
                  <i class="la la-angle-down fs-12"></i>
               @endif
            </a>
            @if($exploreMenuCategories->isNotEmpty())
               <ul class="sub-menu">
                  <li>
                     <a href="{{ route('bundle-programs') }}">{{ __('Unlimited & Bundle Programs') }}</a>
                  </li>
                  @foreach($exploreMenuCategories as $category)
                     @php
                        $hasSubcategories = $category->subcategories->isNotEmpty();
                        $categoryUrl = $buildCourseUrl(['category' => $category->slug]);
                     @endphp
                     <li class="{{ $hasSubcategories ? 'has-children' : '' }}">
                        <a href="{{ $categoryUrl }}">
                           {{ $category->name }}
                           @if($hasSubcategories)
                              <i class="la la-angle-right fs-12"></i>
                           @endif
                        </a>
                        @if($hasSubcategories)
                           <ul class="sub-menu">
                              <li>
                                 <a href="{{ $categoryUrl }}" class="fw-semibold">
                                    {{ __('All :category Courses', ['category' => $category->name]) }}
                                 </a>
                              </li>
                              @foreach($category->subcategories as $subcategory)
                                 @php
                                    $hasChildCategories = $subcategory->childCategories->isNotEmpty();
                                    $subcategoryUrl = $buildCourseUrl([
                                        'category' => $category->slug,
                                        'subcategory' => $subcategory->slug,
                                    ]);
                                 @endphp
                                 <li class="{{ $hasChildCategories ? 'has-children' : '' }}">
                                    <a href="{{ $subcategoryUrl }}">
                                       {{ $subcategory->name }}
                                       @if($hasChildCategories)
                                          <i class="la la-angle-right fs-12"></i>
                                       @endif
                                    </a>
                                    @if($hasChildCategories)
                                       <ul class="sub-menu">
                                          <li>
                                             <a href="{{ $subcategoryUrl }}" class="fw-semibold">
                                                {{ __('All :subcategory Courses', ['subcategory' => $subcategory->name]) }}
                                             </a>
                                          </li>
                                          @foreach($subcategory->childCategories as $childCategory)
                                             @php
                                                $childUrl = $buildCourseUrl([
                                                    'category' => $category->slug,
                                                    'subcategory' => $subcategory->slug,
                                                    'child' => $childCategory->slug,
                                                ]);
                                             @endphp
                                             <li><a href="{{ $childUrl }}">{{ $childCategory->name }}</a></li>
                                          @endforeach
                                       </ul>
                                    @endif
                                 </li>
                              @endforeach
                           </ul>
                        @endif
                     </li>
                  @endforeach
               </ul>
            @endif
         </li>
         <li>
            <a href="{{ route('price.plan') }}">{{ __('Price & Plan') }}</a>
         </li>
         <li>
            <a href="{{ route('course.categories') }}">{{ __('Course Categories') }}</a>
         </li>
         <li>
            <a href="{{ route('consultation.step1') }}">{{ __('Book Consultation') }}</a>
         </li>
         <li>
            <a href="{{ route('all.blogs') }}">{{ __('Blogs') }}</a>
         </li>
         <li>
            <a href="{{ route('contact.us') }}">{{ __('Contact') }}</a>
         </li>
      </ul>
   </div>
   <!-- end off-canvas-menu -->
   <div class="off-canvas-menu custom-scrollbar-styled category-off-canvas-menu">
      <div class="off-canvas-menu-close cat-menu-close icon-element icon-element-sm shadow-sm" data-toggle="tooltip" data-placement="left" title="Close menu">
         <i class="la la-times"></i>
      </div>
      <!-- end off-canvas-menu-close -->
      <ul class="generic-list-item off-canvas-menu-list pt-90px">
         <li>
            <a href="{{ route('bundle-programs') }}">{{ __('Unlimited & Bundle Programs') }}</a>
         </li>
         @forelse($exploreMenuCategories as $category)
            @php
               $hasSubcategories = $category->subcategories->isNotEmpty();
               $categoryUrl = $buildCourseUrl(['category' => $category->slug]);
            @endphp
            <li class="{{ $hasSubcategories ? 'has-children' : '' }}">
               <a href="{{ $categoryUrl }}">
                  {{ $category->name }}
                  @if($hasSubcategories)
                     <i class="la la-angle-right fs-12"></i>
                  @endif
               </a>
               @if($hasSubcategories)
                  <ul class="sub-menu">
                     <li>
                        <a href="{{ $categoryUrl }}" class="fw-semibold">
                           {{ __('All :category Courses', ['category' => $category->name]) }}
                        </a>
                     </li>
                     @foreach($category->subcategories as $subcategory)
                        @php
                           $hasChildCategories = $subcategory->childCategories->isNotEmpty();
                           $subcategoryUrl = $buildCourseUrl([
                               'category' => $category->slug,
                               'subcategory' => $subcategory->slug,
                           ]);
                        @endphp
                        <li class="{{ $hasChildCategories ? 'has-children' : '' }}">
                           <a href="{{ $subcategoryUrl }}">
                              {{ $subcategory->name }}
                              @if($hasChildCategories)
                                 <i class="la la-angle-right fs-12"></i>
                              @endif
                           </a>
                           @if($hasChildCategories)
                              <ul class="sub-menu child-menu">
                                 <li>
                                    <a href="{{ $subcategoryUrl }}" class="fw-semibold">
                                       {{ __('All :subcategory Courses', ['subcategory' => $subcategory->name]) }}
                                    </a>
                                 </li>
                                 @foreach($subcategory->childCategories as $childCategory)
                                    @php
                                       $childUrl = $buildCourseUrl([
                                           'category' => $category->slug,
                                           'subcategory' => $subcategory->slug,
                                           'child' => $childCategory->slug,
                                       ]);
                                    @endphp
                                    <li><a href="{{ $childUrl }}">{{ $childCategory->name }}</a></li>
                                 @endforeach
                              </ul>
                           @endif
                        </li>
                     @endforeach
                  </ul>
               @endif
            </li>
         @empty
            <li class="px-4 py-3 text-muted">{{ __('No categories available yet.') }}</li>
         @endforelse
      </ul>
   </div>
   <!-- end off-canvas-menu -->
   <div class="mobile-search-form">
      <div class="d-flex align-items-center">
         <form method="GET" action="{{ route('search') }}" class="flex-grow-1 me-3 search-form">
             <div class="form-group mb-0">
                <input class="form-control form--control ps-3" type="text" name="search" value="{{ request('search', '') }}" placeholder="Search for anything" />
               <button type="submit" class="search-icon" aria-label="Search">
                  <i class="la la-search"></i>
               </button>
             </div>
          </form>
         <div class="search-bar-close icon-element icon-element-sm shadow-sm">
            <i class="la la-times"></i>
         </div>
         <!-- end off-canvas-menu-close -->
      </div>
   </div>
   <!-- end mobile-search-form -->
   <div class="body-overlay"></div>
</header>











