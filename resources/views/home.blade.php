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
          <!-- ログイン済みユーザー向け: お店の投稿を促す -->
          <a href="" class="btn-secondary">新しいお店を投稿</a>
        @else
          <!-- 未ログインユーザー向け: 会員登録を促す -->
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
    <!-- 未ログインユーザーのみ会員登録の案内を表示 -->
    <div class="register-section">
      <p class="register-text">会員登録して、お店をシェアしよう！</p>
      <a href="{{ route('signup') }}" class="btn-register">無料で登録する</a>
    </div>
  @endguest

  @auth
    <!-- ログイン済みユーザー向け: おすすめのお店を投稿する -->
    <div class="register-section">
      <p class="register-text">あなたのおすすめのお店を投稿しよう！</p>
      <a href="" class="btn-register">お店を投稿する</a>
    </div>
  @endauth
@endsection
