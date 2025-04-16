@extends('layouts.app')
@section('title', 'マイページ')
@section('content')
  <div class="mypage-container">
    <h2>{{ $user->name }}さんのマイページ</h2>

    {{-- タブ切り替え --}}
    <div class="tab-wrapper">
      <ul class="tab-menu">
        <li class="active" data-tab="my-stores">
          自分の投稿
          <span class="tab-badge">{{ $user->stores->count() }}</span>
        </li>
        <li data-tab="liked-stores">
          いいねした店舗
          <span class="tab-badge">{{ $likedStores->count() }}</span>
        </li>
      </ul>
    </div>

    {{-- 自分の店舗一覧 --}}
    <div class="tab-content active" id="my-stores">
      @if ($myStores->count())
      {{-- 投稿アクションボタン --}}
        <div class="mypage-actions">
          <a href="{{ route('store.create') }}" class="btn-add-post">＋ さらに投稿する</a>

          <form action="{{ route('stores.bulkDelete') }}" method="POST" onsubmit="return confirm('本当にすべて削除しますか？')" class="delete-all-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="mypage-btn-delete-all">全て削除する</button>
          </form>
        </div>
        <div class="mypage-store-grid">
          @foreach ($myStores as $store)
            <div class="mypage-store-card">
              <a href="{{ route('stores.show', ['id' => $store->id]) }}">
                <img src="{{ $store->image ?: asset('images/noimage.png') }}" alt="{{ $store->name }}" class="mypage-store-image">
                <div class="mypage-store-info">
                  <h3>{{ $store->name }}</h3>
                  <p class="mypage-store-address">{{ preg_replace('/^日本、/', '', $store->address) }}</p>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        <div class="mypage-pagination-wrapper">
          {{ $myStores->links() }}
        </div>
        @else
          <div class="no-post-box">
            <p class="no-post-text">まだ店舗を投稿していません。</p>
            <p class="no-post-subtext">あなたのおすすめのお店を共有しよう！</p>
            <a href="{{ route('store.create') }}" class="btn-post-first">＋ 最初の投稿をする</a>
          </div>
        @endif
    </div>

    {{-- いいねした店舗（今は非表示） --}}
    <div class="tab-content" id="liked-stores">
      <p>いいねした店舗は現在ありません。</p>
    </div>
  </div>

  @push('scripts')
  <script>
    document.querySelectorAll('.tab-menu li').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-menu li').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
      });
    });
  </script>
  @endpush
  {{-- 退会リンク --}}
  <div class="mypage-delete-account-wrapper">
    <form action="{{ route('user.delete', ['id' => Auth::id()]) }}" method="POST" onsubmit="return confirm('本当に退会しますか？')">
      @csrf
      @method('DELETE')
      <button type="submit" class="mypage-btn-withdraw">退会する</button>
    </form>
  </div>
@endsection
