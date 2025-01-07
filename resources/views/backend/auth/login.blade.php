<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Admin Login</title>
    <!-- CSS files -->
    <link href="{{ asset('backend/dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/dist/css/tabler-flags.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/dist/css/tabler-payments.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/dist/css/demo.min.css') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>
<body class=" d-flex flex-column">
<script src="{{ asset('backend/dist/js/demo-theme.min.js') }}"></script>
<div class="page page-center">
    <div class="container container-normal py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg">
                <div class="container-tight">
                    <div class="text-center mb-4">
                        <a href="#" class="navbar-brand navbar-brand-autodark">
                            <img src="{{ asset('backend/static/logo.svg') }}" height="36" alt="Logo">
                        </a>
                    </div>
                    <div class="card card-md">
                        <div class="card-body">
                            <h2 class="h2 text-center mb-4">Login to your account</h2>
                            <form method="POST" action="{{ route('verwaltung.login') }}" autocomplete="off" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Email address</label>
                                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">
                                        Password
                                        <span class="form-label-description">
                                            <a href="#">I forgot password</a>
                                        </span>
                                    </label>
                                    <div class="input-group input-group-flat">
                                        <input type="password" name="password" class="form-control" placeholder="Your password" required>
                                        <span class="input-group-text">
                                            <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-check">
                                        <input type="checkbox" class="form-check-input" name="remember"/>
                                        <span class="form-check-label">Remember me on this device</span>
                                    </label>
                                </div>
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center text-secondary mt-3">
                        Don't have account yet? <a href="#" tabindex="-1">Sign up</a>
                    </div>
                </div>
            </div>
            <div class="col-lg d-none d-lg-block">
                <img src="{{ asset('backend/static/illustrations/undraw_secure_login_pdn4.svg') }}" height="300" class="d-block mx-auto" alt="">
            </div>
        </div>
    </div>
</div>
<!-- Tabler Core -->
<script src="{{ asset('backend/dist/js/tabler.min.js') }}" defer></script>
<script src="{{ asset('backend/dist/js/demo.min.js') }}" defer></script>
</body>
</html>
