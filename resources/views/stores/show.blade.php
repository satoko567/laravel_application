@extends('layouts.app')
@section('title', $store->name)
@section('content')
  <div class="store-detail-container">
    
    <div class="store-detail-cta-post-again">
      @if (session('success'))
        <a href="{{ route('store.create') }}" class="store-detail-btn-post-again">さらにお店を投稿する</a>
      @else
        <a href="{{ route('store.create') }}" class="store-detail-btn-post-again">おすすめのお店を投稿する</a>
      @endif

      @if (Auth::check())
        <a href="{{ route('user.show', Auth::id()) }}" class="store-detail-btn-my-posts">あなたの投稿をチェック</a>
      @endif
    </div>

    <div class="store-detail-card">
      @if ($store->image)
        <img src="{{ $store->image }}" alt="{{ $store->name }}" class="store-detail-image">
      @endif

      <div class="store-detail-info">
        <h2 class="store-detail-name">{{ $store->name }}</h2>

        <p class="store-detail-address">住所：{{ $store->address }}</p>

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

        <p class="store-detail-registered">投稿者：{{ $store->user->name }}</p>
      </div>

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
