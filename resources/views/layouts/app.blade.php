<!DOCTYPE html>
<html lang="ja">
    <head>
      <meta charset="utf-8">
      <title>@yield('title', 'みんなの三条グルメ')</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
      @stack('styles')
    </head>
    <body>
      @include('commons.header')
      <main class="container">
        @include('commons.error_messages')
        @include('commons.flash_messages')
        @yield('content')
      </main>
      @include('commons.footer')
      <script src="{{ asset('js/script.js') }}"></script>
    </body>
</html>