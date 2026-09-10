<!DOCTYPE html>
<html lang="id">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Aplikasi Persediaan Barang Gudang Material dengan Laravel" />
  <meta name="author" content="Gudang RAFATEK" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Aplikasi Persediaan Barang Gudang Material') - Gudang RAFATEK</title>
  <link rel="icon" href="{{ asset('assets/img/logo.jpg') }}" type="image/x-icon" />
  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
  <script>
    WebFont.load({
      google: { "families": ["Lato:300,400,700,900"] },
      custom: { "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset('assets/css/fonts.min.css') }}'] },
      active: function() { sessionStorage.fonts = true; }
    });
  </script>
  <link rel="stylesheet" href="{{ asset('assets/js/plugin/datepicker/css/bootstrap-datepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/js/plugin/chosen/css/chosen.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/atlantis.min.css') }}">
  <script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
  @stack('css')
</head>
<body>
  <div class="wrapper">
    <div class="main-header">
      <div class="logo-header" data-background-color="blue">
        <a href="{{ route('dashboard') }}" class="logo">
          <div class="navbar-brand">
            <span><img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo" style="height:25px; margin-left:5px;"></span>
            <span class="text-white brand-text" style="display:inline-block; font-size:14px;">Gudang RAFATEK</span>
          </div>
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"><i class="icon-menu"></i></span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
          <button class="btn btn-toggle toggle-sidebar"><i class="icon-menu"></i></button>
        </div>
      </div>
      <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
        <div class="container-fluid">
          <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
            <li class="nav-item dropdown hidden-caret">
              <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                <div class="avatar-sm-top mt-1">
                  @php
                    $foto = Auth::user()->foto_profil ?? null;
                    $fotoHeader = ($foto && file_exists(public_path('assets/img/profiles/'.$foto))) ? asset('assets/img/profiles/'.$foto) : asset('assets/img/avatar-1.png');
                    $fotoDropdown = ($foto && file_exists(public_path('assets/img/profiles/'.$foto))) ? asset('assets/img/profiles/'.$foto) : asset('assets/img/avatar-2.png');
                    $fotoSidebar = ($foto && file_exists(public_path('assets/img/profiles/'.$foto))) ? asset('assets/img/profiles/'.$foto) : asset('assets/img/avatar-2.png');
                  @endphp
                  <img src="{{ $fotoHeader }}" alt="image profile" class="avatar-img rounded-circle">
                  <i class="fas fa-angle-down avatar-title"></i>
                </div>
              </a>
              <ul class="dropdown-menu dropdown-user animated fadeIn">
                <li>
                  <div class="user-box">
                    <div class="avatar-lg"><img src="{{ $fotoDropdown }}" alt="image profile" class="avatar-img rounded"></div>
                    <div class="u-text pt-1">
                      <h4>{{ Auth::user()->nama_user }}</h4>
                      <p class="text-muted">{{ Auth::user()->hak_akses }}</p>
                    </div>
                  </div>
                </li>
                <li>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{ route('profil.edit') }}"><i class="fas fa-user-edit mr-1"></i> Edit Profil</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="{{ route('password.edit') }}"><i class="fas fa-user-lock mr-1"></i> Ubah Password</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalLogout"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="sidebar sidebar-style-2">
      <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
          <div class="user">
            <div class="avatar-sm float-left mr-2">
              <img src="{{ $fotoSidebar }}" alt="image profile" class="avatar-img rounded-circle">
            </div>
            <div class="info">
              <a><span>{{ Auth::user()->nama_user }}<span class="user-level">{{ Auth::user()->hak_akses }}</span></span></a>
            </div>
          </div>
          <ul class="nav nav-secondary">
            @include('partials.sidebar')
          </ul>
        </div>
      </div>
    </div>

    <div class="main-panel">
      <div class="content">
        @if(session('success'))
          <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
            <span data-notify="icon" class="fas fa-check"></span>
            <span data-notify="title" class="text-success">Sukses!</span>
            <span data-notify="message">{{ session('success') }}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
            <span data-notify="icon" class="fas fa-times"></span>
            <span data-notify="title" class="text-danger">Gagal!</span>
            <span data-notify="message">{{ session('error') }}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
        @endif
        @if($errors->any())
          <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
            <span data-notify="icon" class="fas fa-times"></span>
            <span data-notify="title" class="text-danger">Gagal!</span>
            <span data-notify="message">{{ $errors->first() }}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
        @endif
        @yield('content')
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-sign-out-alt mr-2"></i>Logout</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">Apakah Anda yakin ingin logout?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-round" data-dismiss="modal">Batal</button>
          <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf<a href="{{ route('logout') }}" class="btn btn-danger btn-round" onclick="event.preventDefault(); this.closest('form').submit();">Ya, Logout</a></form>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/chosen/js/chosen.jquery.js') }}"></script>
  <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin.js') }}"></script>
  <script src="{{ asset('assets/js/form-validation.js') }}"></script>
  @stack('js')
</body>
</html>
