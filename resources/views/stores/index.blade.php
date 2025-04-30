@extends('layouts.app')
@section('title', '店舗一覧')

@section('content')
  <div class="store-index-container">
    <h2 class="store-index-title">人気のお店ランキング</h2>

    <div class="store-index-description">
      <p>ユーザーから人気のあるお店を、いいねの数が多い順にご紹介しています！</p>
      <p>あなたのおすすめのお店もぜひ投稿して、みんなにシェアしましょう！</p>
      @auth
        <a href="{{ route('store.create') }}" class="store-index-btn-post">＋ おすすめのお店を投稿する</a>
      @else
        <a href="{{ route('signup') }}" class="store-index-btn-register">無料会員登録して投稿する</a>
      @endauth
    </div>

    <div class="store-index-grid">
      @foreach ($stores as $store)
        @include('components.user_store_card', ['store' => $store])
      @endforeach
    </div>

    <div class="store-index-pagination-wrapper">
      {{ $stores->links() }}
    </div>
  </div>
@endsection
