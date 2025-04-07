@extends('layouts.app')
@section('title', 'ログイン')
@section('content')
    <div class="login-hero">
        <div class="login-hero-bg"></div>
        <div class="login-hero-content">
            <img src="/images/login-image.jpg" alt="ログイン" class="login-image">
            <h2>ログイン</h2>
            <p>アカウントにログインして、三条のグルメを楽しもう！</p>
        </div>
    </div>

    <section class="login-form-section">
        <h3>ログイン</h3>
        <form method="POST" action="{{ route('login.post') }}" class="login-form">
            @csrf
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="text" name="email" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password">
            </div>

            <button type="submit" class="btn-login">ログイン</button>
        </form>
    </section>
@endsection