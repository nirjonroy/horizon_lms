<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('admin.dashboard.index')}}" class="brand-link">
      <img src="{{asset('backend/imgs/logo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Horizons</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('backend/imgs/logo.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div> --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{route('admin.dashboard.index')}}" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                {{-- <i class="right fas fa-angle-left"></i> --}}
              </p>
            </a>
            {{-- <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index.html" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index2.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index3.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v3</p>
                </a>
              </li>
            </ul> --}}
          </li>
          <li class="nav-item">
            <a href="{{route('admin.siteinfo.index')}}" class="nav-link">
              <i class="nav-icon fas fa-industry"></i>
              <p>
                Site Information
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>
            <li class="nav-item">
            <a href="{{ route('admin.pages.index') }}" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                Custom Pages
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-school"></i>
              {{-- <i class="fa-solid fa-person-chalkboard"></i> --}}
              <p>
                Where To Study
                <i class="fas fa-angle-left right"></i>
                {{-- <span class="badge badge-info right">6</span> --}}
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.whereToStudy.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>study options</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.whereToStudy.create')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Premium Course Catalog
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.courses.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Courses</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.premium-course-categories.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Categories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.premium-course-subcategories.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Subcategories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.premium-course-child-categories.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Child Categories</p>
                </a>
              </li>
            </ul>
          </li>

          <!--<li class="nav-item has-treeview">-->
          <!--  <a href="#" class="nav-link">-->
          <!--    <i class="nav-icon fas fa-life-ring"></i>-->
          <!--    <p>-->
          <!--      Student life-->
          <!--      <i class="fas fa-angle-left right"></i>-->
          <!--      {{-- <span class="badge badge-info right">6</span> --}}-->
          <!--    </p>-->
          <!--  </a>-->
          <!--  <ul class="nav nav-treeview">-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.internationalStdLife.index')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>Student life</p>-->
          <!--      </a>-->
          <!--    </li>-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.studentlife.create')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>Create</p>-->
          <!--      </a>-->
          <!--    </li>-->

          <!--  </ul>-->
          <!--</li>-->

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-wallet"></i>
              <p>
                Fees and Cost
                <i class="fas fa-angle-left right"></i>
                {{-- <span class="badge badge-info right">6</span> --}}
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.fees.online.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fees</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.fees.category.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>FeesCategory</p>
                </a>
              </li>



            </ul>
          </li>

          <!--<li class="nav-item has-treeview">-->
          <!--  <a href="#" class="nav-link">-->
          <!--    <i class="nav-icon fas fa-copy"></i>-->
          <!--    <p>-->
          <!--      important Dates-->
          <!--      <i class="fas fa-angle-left right"></i>-->
          <!--      {{-- <span class="badge badge-info right">6</span> --}}-->
          <!--    </p>-->
          <!--  </a>-->
          <!--  <ul class="nav nav-treeview">-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.internationalStdLife.index')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>important Date</p>-->
          <!--      </a>-->
          <!--    </li>-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.studentlife.create')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>Create</p>-->
          <!--      </a>-->
          <!--    </li>-->

          <!--  </ul>-->
          <!--</li>-->

          <!--<li class="nav-item has-treeview">-->
          <!--  <a href="#" class="nav-link">-->
          <!--    <i class="nav-icon fas fa-copy"></i>-->
          <!--    <p>-->
          <!--      refer and earn-->
          <!--      <i class="fas fa-angle-left right"></i>-->
          <!--      {{-- <span class="badge badge-info right">6</span> --}}-->
          <!--    </p>-->
          <!--  </a>-->
          <!--  <ul class="nav nav-treeview">-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.internationalStdLife.index')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>refer</p>-->
          <!--      </a>-->
          <!--    </li>-->
          <!--    <li class="nav-item">-->
          <!--      <a href="{{route('admin.studentlife.create')}}" class="nav-link">-->
          <!--        <i class="far fa-circle nav-icon"></i>-->
          <!--        <p>Create</p>-->
          <!--      </a>-->
          <!--    </li>-->

          <!--  </ul>-->
          <!--</li>-->


          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Blog
                <i class="fas fa-angle-left right"></i>
                {{-- <span class="badge badge-info right">6</span> --}}
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.blog.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Blog</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.blog.create')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>

            </ul>
          </li>
          
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Web Inner
                <i class="fas fa-angle-left right"></i>
                {{-- <span class="badge badge-info right">6</span> --}}
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.webinner.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All WebInner</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.webinner.create')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-sliders-h"></i>
              
              <p>
                Slider
                <i class="fas fa-angle-left right"></i>
                {{-- <span class="badge badge-info right">6</span> --}}
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.slider.index')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Slider</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.slider.create')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item">
            <a href="{{ route('admin.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-comments"></i>
              <p>Testimonials</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('admin.home-highlights.index') }}" class="nav-link {{ request()->routeIs('admin.home-highlights.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-lightbulb"></i>
              <p>Home Highlights</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('admin.partners.index') }}" class="nav-link {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-handshake"></i>
              <p>Partners</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('admin.about.index')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                About Us
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('admin.studentInformation.index')}}" class="nav-link">
              <i class="nav-icon fas fa-id-card"></i>
              <p>
                Student Informations
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('admin.consultation.index')}}" class="nav-link">
              <i class="nav-icon fas fa-phone"></i>
              <p>
                Consultation
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Orders
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.coupons.index') }}" class="nav-link">
              <i class="nav-icon fas fa-ticket-alt"></i>
              <p>Coupons</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="{{ route('admin.campaigns.index') }}" class="nav-link">
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>Campaigns</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('admin.contact.index')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Contact Form
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="{{route('admin.script.index')}}" class="nav-link">
              <i class="nav-icon fas fa-flask"></i>
              <p>
                Scripts 
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('admin.seo-setup')}}" class="nav-link">
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>
                SEO 
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>
        
            <li class="nav-item">
            <a href="{{ route('admin.redirects.index') }}" class="nav-link">
              <i class="nav-icon fas fa-random"></i>
              <p>
                Redirects
              </p>
            </a>
          </li>        
            
          <li class="nav-item">
            <a href="{{route('admin.backend.user.registration')}}" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>
                user
                {{-- <span class="right badge badge-danger">New</span> --}}
              </p>
            </a>
          </li>

          <li class="nav-item">
            <form id="clear-cache-form" action="{{ route('admin.cache.clear') }}" method="POST" style="display: none;">
              @csrf
            </form>
            <a href="#" class="nav-link" onclick="event.preventDefault(); if (confirm('Clear application cache?')) { document.getElementById('clear-cache-form').submit(); }">
              <i class="nav-icon fas fa-broom"></i>
              <p>Clear Cache</p>
            </a>
          </li>

          <li>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-success">
              <i class="fa fa-sign-out" aria-hidden="true"></i> 
              Logout
            </a>

          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
