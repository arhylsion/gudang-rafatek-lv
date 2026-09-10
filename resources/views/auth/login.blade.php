<!DOCTYPE html>
<html lang="id">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Aplikasi Persediaan Barang Gudang Material" />
  <title>Aplikasi Persediaan Barang Gudang Rafatek</title>
  <link rel="icon" href="{{ asset('assets/img/logo.jpg') }}" type="image/x-icon" />
  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
  <script>
    WebFont.load({
      google: { "families": ["Lato:300,400,700,900"] },
      custom: { "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset('assets/css/fonts.min.css') }}'] },
      active: function() { sessionStorage.fonts = true; }
    });
  </script>
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/atlantis.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>
<body class="login">
  @if(request('pesan') == 1 || session('error'))
    <div class="alert alert-notify alert-danger alert-dismissible fade show" role="alert">
      <span data-notify="icon" class="fas fa-times"></span>
      <span data-notify="title" class="text-danger">Gagal Login!</span>
      <span data-notify="message">{{ session('error', 'Username/Email atau Password salah. Cek kembali Username/Email dan Password Anda.') }}</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
  @elseif(request('pesan') == 2)
    <div class="alert alert-notify alert-warning alert-dismissible fade show" role="alert">
      <span data-notify="icon" class="fas fa-exclamation"></span>
      <span data-notify="title" class="text-warning">Peringatan!</span>
      <span data-notify="message">Anda harus login terlebih dahulu.</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
  @elseif(request('pesan') == 3 || session('success'))
    <div class="alert alert-notify alert-success alert-dismissible fade show" role="alert">
      <span data-notify="icon" class="fas fa-check"></span>
      <span data-notify="title" class="text-success">Sukses!</span>
      <span data-notify="message">{{ session('success', 'Anda telah berhasil logout.') }}</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
  @endif

  <div class="wrapper wrapper-login">
    <div class="container container-login animated fadeIn">
      <div class="text-center mb-4"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo" width="95px"></div>
      <h3 class="text-center">Aplikasi Persediaan Barang <br>Gudang Rafatek</h3>
      <form action="{{ route('login.post') }}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="form-group form-floating-label">
          <div class="user-icon"><i class="fas fas fa-user"></i></div>
          <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control input-border-bottom" autocomplete="off" required>
          <label for="username" class="placeholder">Username atau Email</label>
          <div class="invalid-feedback">Username atau email tidak boleh kosong.</div>
        </div>
        <div class="form-group form-floating-label">
          <div class="user-icon"><i class="fas fa-lock"></i></div>
          <div class="show-password"><i class="flaticon-interface"></i></div>
          <input type="password" id="password" name="password" class="form-control input-border-bottom" autocomplete="off" required>
          <label for="password" class="placeholder">Password</label>
          <div class="invalid-feedback">Password tidak boleh kosong.</div>
        </div>
        <div class="form-action mt-2">
          <input type="submit" name="login" value="LOGIN" class="btn btn-secondary btn-rounded btn-login btn-block">
        </div>
      </form>
    </div>
  </div>

  <script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/ready.js') }}"></script>
  <script src="{{ asset('assets/js/form-validation.js') }}"></script>
</body>
</html>
