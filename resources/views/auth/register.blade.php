@extends('layouts.app')
@section('title', '会員登録')
@section('content')
    <div class="register-hero">
        <div class="register-hero-bg"></div>
        <div class="register-hero-content">
            <img src="/images/register-image.jpg" alt="ランチ" class="register-image">
            <h2>新規会員登録</h2>
            <p>会員登録して、三条のグルメをシェアしよう！</p>
        </div>
    </div>

    <section class="register-form-section">
        <h3>会員登録フォーム</h3>
        <form method="POST" action="{{ route('signup.post') }}" class="register-form">
            @csrf
            <div class="form-group">
                <label for="name">お名前</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="text" name="email" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" value="{{ old('password') }}">
            </div>

            <div class="form-group">
                <label for="password_confirmation">パスワード確認</label>
                <input id="password_confirmation" type="password" name="password_confirmation" value="{{ old('password_confirmation') }}">
            </div>

            <button type="submit" class="btn-register">登録する</button>
        </form>

        <div class="login-link">
            <p>登録済みの方はこちら</p>
            <a href="{{ route('login') }}" class="btn-login-link">ログイン</a>
        </div>
    </section>

@endsection