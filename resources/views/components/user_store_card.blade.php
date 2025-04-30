<div class="user-store-card">
  <a href="{{ route('stores.show', ['id' => $store->id]) }}">
    <img src="{{ asset($store->image ?: 'images/noimage.png') }}" alt="{{ $store->name }}" class="user-store-image">
    <div class="user-store-info">
      <h3>{{ $store->name }}</h3>

      @if (isset($store->user) && empty($isMine))
        <p class="user-store-poster">
          <a href="{{ route('user.show', ['id' => $store->user->id]) }}">
            投稿者：{{ $store->user->name }}さん
          </a>
        </p>
        @elseif (!empty($isMine) && Auth::check() && Auth::id() === $store->user->id)
          <p class="user-store-poster">投稿者：あなた</p>
        @endif

      <p class="user-store-address">
        <a href="{{ $store->google_map_url }}" target="_blank">
          {{
            preg_replace(
              ['/^日本、?/', '/〒\d{3}-\d{4}\s*/', '/新潟県/'],
              '',
              $store->address
            )
          }}
        </a>
      </p>

      {{-- いいね表示 --}}
      @include('components.like_button', ['store' => $store, 'tab' => $tab ?? null])
    </div>
  </a>
</div>
