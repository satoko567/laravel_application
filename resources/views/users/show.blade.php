@extends('layouts.app')
@section('title', 'マイページ')
@section('content')
<div class="user-detail-container">
  @if (Auth::id() === $user->id)
    <h2>{{ $user->name }}さんのマイページ</h2>
  @else
    <h2>{{ $user->name }}さんの投稿一覧</h2>
  @endif

  {{-- タブ切り替え --}}
  <div class="tab-wrapper">
    <ul class="tab-menu">
      <li class="active" data-tab="my-stores">
        @if (Auth::id() === $user->id)
          自分の投稿
        @else
          {{ $user->name }}さんの投稿
        @endif
        <span class="tab-badge">{{ $myStoreCount }}</span>
      </li>
      @if (Auth::id() === $user->id)
        <li data-tab="liked-stores">
          いいねした店舗
          <span class="tab-badge">{{ $likedStoreCount }}</span>
        </li>
      @endif
    </ul>
  </div>

  {{-- 自分の投稿一覧 --}}
  <div class="tab-content active" id="my-stores">
    @if ($myStores->count())
      @if (Auth::id() === $user->id)
        <div class="user-detail-actions">
          <a href="{{ route('store.create') }}" class="btn-add-post">＋ さらに投稿する</a>
          <form action="{{ route('stores.bulkDelete') }}" method="POST" onsubmit="return confirm('本当にすべて削除しますか？')" class="delete-all-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="user-detail-btn-delete-all">全て削除する</button>
          </form>
        </div>
      @endif

      <div class="user-detail-store-grid">
        @foreach ($myStores as $store)
          @include('components.user_store_card', ['store' => $store, 'tab' => 'my-stores', 'isMine' => Auth::id() === $user->id])
        @endforeach
      </div>

      <div class="user-detail-pagination-wrapper">
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

  {{-- いいねした店舗一覧 --}}
  @if (Auth::id() === $user->id)
    <div class="tab-content" id="liked-stores">
      @if ($likedStores->count())
        <div class="user-detail-store-grid">
          @foreach ($likedStores as $store)
            @include('components.user_store_card', ['store' => $store, 'tab' => 'liked-stores'])
          @endforeach
        </div>
        <div class="user-detail-pagination-wrapper">
          {{ $likedStores->appends(['tab' => 'liked-stores'])->links() }}
        </div>
      @else
        <div class="no-post-box">
          <p class="no-post-text">まだ「いいね」した店舗はありません。</p>
          <p class="no-post-subtext">気になるお店を見つけて、いいねしてみましょう！</p>
          <a href="{{ route('stores.index') }}" class="btn-post-first">店舗を探す</a>
        </div>
      @endif
    </div>
  @endif

  {{-- 退会リンク --}}
  @if (Auth::id() === $user->id)
    <div class="user-detail-delete-account-wrapper">
      <form action="{{ route('user.delete', ['id' => Auth::id()]) }}" method="POST" onsubmit="return confirm('本当に退会しますか？')">
        @csrf
        @method('DELETE')
        <button type="submit" class="user-detail-btn-withdraw">退会する</button>
      </form>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabParam = new URLSearchParams(window.location.search).get('tab');
    const tabs = document.querySelectorAll('.tab-menu li');
    const contents = document.querySelectorAll('.tab-content');

    if (tabParam) {
      tabs.forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.tab === tabParam) {
          tab.classList.add('active');
        }
      });

      contents.forEach(content => {
        content.classList.remove('active');
        if (content.id === tabParam) {
          content.classList.add('active');
        }
      });
    }

    document.querySelectorAll('.tab-menu li').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-menu li').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');

        const flash = document.getElementById('flash-message');
        if (flash) {
          flash.style.display = 'none';
        }
      });
    });
  });
</script>
@endpush
