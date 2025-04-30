<div class="like-wrapper">
  @auth
    @if (Auth::user()->isLike($store->id))
      <form action="{{ route('stores.unlike', ['id' => $store->id]) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        @if (!empty($tab))
          <input type="hidden" name="tab" value="{{ $tab }}">
        @endif
        <button type="submit" class="like-button liked">♥ いいね済み</button>
      </form>
    @else
      <form action="{{ route('stores.like', ['id' => $store->id]) }}" method="POST" style="display:inline;">
        @csrf
        @if (!empty($tab))
          <input type="hidden" name="tab" value="{{ $tab }}">
        @endif
        <button type="submit" class="like-button">♡ いいね</button>
      </form>
    @endif
  @else
    <span class="like-count guest">
      ♥ {{ $store->likedUsers()->count() }}件のいいね
    </span>
  @endauth

  @auth
    <span class="like-count">（{{ $store->likedUsers()->count() }}件のいいね）</span>
  @endauth
</div>
