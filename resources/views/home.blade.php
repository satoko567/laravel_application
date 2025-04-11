@extends('layouts.app')
@section('title', 'トップページ')
@section('content')
  <div class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <h2>三条のグルメをみんなでシェアしよう！</h2>
      <p>地元の美味しいお店を共有して、もっと三条の魅力を発見しよう！</p>
      <div class="hero-buttons">
        <a href="" class="btn-primary">店舗一覧を見る</a>
        @auth
          <a href="{{ route('store.create') }}" class="btn-secondary">新しいお店を投稿</a>
        @else
          <a href="{{ route('signup') }}" class="btn-secondary">無料で登録</a>
        @endauth
      </div>
    </div>
  </div>
  <section class="about">
    <h3>みんなの三条グルメとは？</h3>
    <p>三条市の美味しいお店をシェアするためのアプリです。<br>地元の人だからこそ知っている隠れた名店をみんなで共有しよう！</p>
  </section>

  @guest
    <div class="register-section">
      <p class="register-text">会員登録して、お店をシェアしよう！</p>
      <a href="{{ route('signup') }}" class="btn-register">無料で登録する</a>
    </div>
  @endguest

  @auth
    <div class="register-section">
      <p class="register-text">あなたのおすすめのお店を投稿しよう！</p>
      <a href="{{ route('store.create') }}" class="btn-register">お店を投稿する</a>
    </div>
  @endauth

  <section class="section-title gourmet-section">
    <div class="section-inner">
      <h3 class="section-heading">みんなが投稿したおすすめグルメ</h3>
      <p class="section-subtext">
        地元ユーザーがシェアした最新のお店をチェックしよう！<br>
        あなたの「行ってみたい」がきっと見つかる。
      </p>
    </div>
    @include('components.latest_user_stores', ['users' => $users])
  </section>

  <section class="cta-post-section">
  <div class="cta-inner">
    <h3 class="cta-heading">あなたも次の投稿者になりませんか？</h3>
    <p class="cta-subtext">お気に入りのお店をぜひシェアして、三条のグルメをもっと盛り上げよう！</p>
    @auth
      <a href="{{ route('store.create') }}" class="btn-cta">お店を投稿する</a>
    @else
      <a href="{{ route('signup') }}" class="btn-cta">無料会員登録して投稿する</a>
    @endauth
  </div>
</section>

@endsection
