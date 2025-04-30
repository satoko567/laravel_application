@extends('layouts.app')
@section('title', $store->name)
@section('content')
  @if (request('fresh'))
    <script>
      location.reload();
    </script>
  @endif
  
  <div class="store-detail-container">

  {{-- ボタン --}}
    <div class="store-detail-cta-post-again">
      @auth
        @if (session('post_success'))
          <a href="{{ route('store.create') }}" class="store-detail-btn-post-again">さらに投稿する</a>
        @else
          <a href="{{ route('store.create') }}" class="store-detail-btn-post-again">お店を投稿する</a>
        @endif
        <a href="{{ route('stores.index') }}" class="store-detail-btn-post-again">人気な投稿をみる</a>
        @else
          <a href="{{ route('login') }}" class="store-detail-btn-post-again">ログインして投稿</a>
          <a href="{{ route('signup') }}" class="store-detail-btn-post-again">無料会員登録</a>
          <a href="{{ route('stores.index') }}" class="store-detail-btn-post-again">人気な投稿をみる</a>
      @endauth
    </div>

    {{-- 店舗詳細 --}}
    <div class="store-detail-card">
      @if ($store->image)
        <img src="{{ asset($store->image ?: 'images/noimage.png') }}" alt="{{ $store->name }}" class="store-detail-image">
      @endif

      <div class="store-detail-info">
        <h2 class="store-detail-name">{{ $store->name }}</h2>

        <p class="store-detail-address">
          住所：{{ preg_replace('/^日本、?/', '', $store->address) }}
        </p>

        @if ($store->phone_number)
          <p class="store-detail-phone">電話番号：{{ $store->phone_number }}</p>
        @endif

        @if ($store->website)
          <p class="store-detail-website">公式サイト：
            <a href="{{ $store->website }}" target="_blank">{{ $store->website }}</a>
          </p>
        @endif

        <p class="store-detail-map-link">
          <a href="{{ $store->google_map_url }}" target="_blank">📍 Googleマップで見る</a>
        </p>

        <p class="store-detail-registered">
          投稿者：
          @if (Auth::check() && Auth::id() === $store->user_id)
            あなた
          @else
            <a href="{{ route('user.show', ['id' => $store->user->id]) }}" class="store-detail-user-link">
              {{ $store->user->name }}さん
            </a>
          @endif
        </p>

        {{-- いいね表示 --}}
        @include('components.like_button', ['store' => $store])
        
        @if (Auth::check() && Auth::id() === $store->user_id)
          <div class="store-detail-actions">
            <a href="{{ route('store.edit', ['id' => $store->id]) }}" class="store-detail-btn-edit">編集する</a>
            
            <form action="{{ route('store.delete', ['id' => $store->id]) }}" method="POST" onsubmit="return confirm('本当に削除しますか？')" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="store-detail-btn-delete">削除する</button>
            </form>
          </div>
        @endif
    </div>
  </div>
@endsection
