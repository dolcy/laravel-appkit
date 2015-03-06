<!DOCTYPE html>
<html lang="en">
  @include('shell::partials.head')
  <body>
    <div class="row">
      @include('shell::components.navigation.left')
      <div class="container-fluid">
          <div class="side-body">
            <div class="row">
              <div class="col-sm-12 {{config('appkit.render_sidebar') ? 'col-md-9' : null}}">
                <h1 class="page-header">
                    @section('title')@show
                    @include('shell::components.navigation.breadcrumbs')
                </h1>
                @include('appkit::errors')
                @include('appkit::flash')
                @yield('content')
              </div>
              @if (config('appkit.render_sidebar'))
                <div class="col-sm-12 col-md-3">
                  @include('shell::components.sidebar.sidebar')
                </div>
              @endif
            </div>
          </div>
      </div>
    </div>
    @include('shell::partials.scripts')
  </body>
</html>