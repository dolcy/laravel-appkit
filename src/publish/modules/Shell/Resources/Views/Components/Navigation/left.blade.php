<div class="side-menu">
    
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <div class="brand-wrapper">
                <!-- Hamburger -->
                <button type="button" class="navbar-toggle">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- Brand -->
                <div class="brand-name-wrapper">
                    <a class="navbar-brand" href="{{ route('admin') }}">
                        {{ config('appkit.app_name') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- Main Menu -->
        <div class="side-menu-container">
            <ul class="nav navbar-nav">
                <li class="widget">
                    <img class="avatar img-circle" src="{{ asset($user_image) }}" />
                    <h4 class="text-center">{{ $user_name }}</h4>
                    <h5 class="text-center" style="opacity:0.8;">{{ $user_email }}</h5>
                    <div class="button-bar">
                        <a href="auth/logout" class="btn btn-default" >
                            <i class="fa fa-sign-out"></i> Sign Out
                        </a>
                        <a href="{{ route('users.edit', $user_id) }}" class="btn btn-default" >
                            <i class="fa fa-user"></i> Profile
                        </a>
                    </div>
                </li>
            @foreach ($menu_admin_left->roots() as $item) 
                <li {!! $item->hasChildren() ? 'class="dropdown panel panel-default"' : null !!} {!! Request::url() == $item->url() ? 'class="active"' : null !!}>
                    <a {!! $item->hasChildren() ? 'data-toggle="collapse" href="#collapse'.$item->slug.'"' : 'href="'.$item->url().'"' !!}>
                        <i class="{{ $item->icon }}"></i> {{ $item->title }}
                    </a>
                    @if ($item->hasChildren())
                        <div id="collapse{{$item->slug}}" class="panel-collapse collapse {{ $item->hasChildUrl(Request::url()) ? 'in' : null }}">
                            <div class="panel-body">
                                <ul class="nav navbar-nav">
                                     @foreach ($item->children() as $child)
                                        <li {!! Request::url() == $child->url() ? 'class="active"' : null !!}>
                                            <a href="{{ $child->url() }}">{{ $child->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
    
</div>