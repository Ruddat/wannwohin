<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ config('site.description') }}">
<meta name="keywords" content="{{ config('site.keywords') }}">
<meta name="author" content="{{ config('site.author') }}">
<link rel="icon" href="{{ asset(config('site.favicon')) }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset(config('site.favicon')) }}" type="image/x-icon">
<title>@yield('title') | {{ config('site.name') }} - Verwaltung</title>
<meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF-Token hinzufügen -->
<meta name="session-id" content="{{ Session::getId() }}">
