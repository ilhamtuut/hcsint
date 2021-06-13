<div class="page-top-bar-area d-flex align-items-center justify-content-between">

    <div class="logo-trigger-search-area d-flex align-items-center">
        <!-- Logo Trigger -->
        <div class="logo-trigger-area d-flex align-items-center">
            <!-- Trigger -->
            <div class="top-trigger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <!-- User Meta -->
    <div class="user-meta-data d-flex align-items-center">
        <!-- Profile -->
        <div class="topbar-profile">

            <!-- Thumb -->
            <div class="user---thumb">
                <img src="{{ asset('images/logo/usr.png') }}" alt="">
                <p class="text-white">{{ucfirst(Auth::user()->username)}} <i class="fa fa-angle-down"></i></p>
            </div>

            <!-- Profile Data -->
            <div class="profile-data">
                <!-- Profile User Details -->
                <div class="profile-user--details" style="background-color: #1e6977;">
                    <!-- Thumb -->
                    <div class="profile---thumb-det">
                        <img src="{{asset('images/logo/usr.png')}}" alt="usr">
                    </div>
                    <!-- Profile Text -->
                    <div class="profile---text-details">
                        <h6>{{ucfirst(Auth::user()->username)}}</h6>
                        <p class="text-warning">{{ucfirst(Auth::user()->roles[0]->display_name)}}</p>
                    </div>
                </div>
                <!-- Profile List Data -->
                <a class="profile-list--data mt-20" href="{{route('user.profile')}}">
                    <!-- Profile icon -->
                    <div class="profile--list-icon">
                        <i class="icon-profile-male text-warning" aria-hidden="true"></i>
                    </div>
                    <!-- Profile Text -->
                    <div class="notification--list-body-text profile">
                        <h6>My profile</h6>
                    </div>
                </a>
                <!-- Profile List Data -->
                <a class="profile-list--data" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <!-- Profile icon -->
                    <div class="profile--list-icon">
                        <i class="fa fa-sign-out text-danger" aria-hidden="true"></i>
                    </div>
                    <!-- Profile Text -->
                    <div class="notification--list-body-text profile">
                        <h6>Sign-out</h6>
                    </div>
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  <input type="submit" value="logout" style="display: none;">
                </form>
            </div>
        </div>
    </div>
</div>
