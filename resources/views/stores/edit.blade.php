@extends('layouts.app')
@section('title', '店舗情報を編集')
@section('content')
  <section class="store-edit-post-section">
    <div class="store-edit-form-container">
      <h2 class="store-edit-form-title">店舗情報を編集する</h2>
      <p class="store-edit-form-subtext">Googleマップの共有リンクを貼り直すことで、情報を更新できます。</p>

      <form method="POST" action="{{ route('store.update', ['id' => $store->id]) }}" class="store-edit-form">
        @csrf
        @method('PUT')

        <div class="store-edit-group">
          <label for="google_map_url">Googleマップの共有URL</label>
          <input type="url" name="google_map_url" id="google_map_url"
                value="{{ old('google_map_url', $store->google_map_url) }}" required>
          <p class="store-edit-note">※ Googleマップの「共有」→「リンクをコピー」で得られるURLを貼ってください。</p>
          @error('google_map_url')
            <div class="store-edit-error-text">⚠ {{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="store-edit-btn-submit">更新する</button>
      </form>
    </div>
  </section>
@endsection
