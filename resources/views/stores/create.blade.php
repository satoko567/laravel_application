@extends('layouts.app')
@section('title', 'おすすめ店舗を投稿')
@section('content')
  <section class="store-create-post-section">
    <div class="store-create-form-container">
      <h2 class="store-create-form-title">おすすめのお店を投稿しよう！</h2>
      <p class="store-create-form-subtext">Googleマップの共有リンクを貼るだけで、簡単にお店を紹介できます！</p>
      <form method="POST" action="{{ route('store.store') }}" class="store-create-post-form">
        @csrf
        <div class="store-create-form-group">
          <label for="google_map_url">Googleマップの共有URL</label>
          <input type="url" name="google_map_url" id="google_map_url" class="form-control" placeholder="https://goo.gl/maps/xxxx" required>
          <p class="store-create-note">※ Googleマップの「共有」→「リンクをコピー」で得られるURLを貼ってください。</p>
        </div>
        <button type="submit" class="store-create-btn-submit">登録する</button>
      </form>
    </div>
  </section>
@endsection
