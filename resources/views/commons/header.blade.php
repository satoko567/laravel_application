<header class="header">
  <div class="header-container">
    <h1 class="logo">
      <a href="{{ url('/') }}">みんなの三条グルメ</a>
    </h1>
    <div class="hamburger" id="hamburger">
    ☰
    </div>
    <nav class="nav-menu" id="nav-menu">
      <ul class="nav-links">
        @guest
          <li><a href="{{ route('signup') }}">会員登録</a></li>
          <li><a href="{{ route('login') }}">ログイン</a></li>
          <li><a href="{{ route('stores.index') }}">店舗一覧</a></li>
        @endguest
        @auth
          <li><a href="{{ route('store.create') }}">新規投稿</a></li>
          <li><a href="{{ route('stores.index') }}">店舗一覧</a></li>
          <li><a href="{{ route('user.show', ['id' => Auth::id()]) }}">マイページ</a></li>
          <li>
            <a href="{{ route('logout') }}" 
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              ログアウト
            </a>
          </li>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        @endauth
      </ul>
    </nav>
  </div>
</header>