<div class="latest-store-grid">
  @foreach($users as $user)
    @if($user->latestStore)
      <div class="latest-store-card">
        <a href="{{ route('stores.show', $user->latestStore->id) }}">
          <img src="{{ asset($user->latestStore->image ?: 'images/noimage.png') }}" alt="{{ $user->latestStore->name }}">
          <p class="latest-store-info">
            <a href="{{ route('stores.show', $user->latestStore->id) }}" class="latest-store-name">
              {{ $user->latestStore->name }}
            </a>
            <br>
            <span class="latest-poster-name">
              <a href="{{ route('user.show', ['id' => $user->id]) }}" class="latest-poster-link">
                投稿者：{{ $user->name }}さん
              </a>
            </span>
          </p>
        </a>
        {{-- いいね表示 --}}
        @include('components.like_button', ['store' => $user->latestStore])

      </div>
    @endif
  @endforeach
</div>

<div class="latest-pagination-wrapper">
  {{ $users->links() }}
</div>

