<div class="latest-store-grid">
  @foreach($users as $user)
    @if($user->latestStore)
      <div class="latest-store-card">
        <a href="{{ route('stores.show', $user->latestStore->id) }}">
          <img src="{{ $user->latestStore->image ?: asset('images/noimage.png') }}" alt="{{ $user->latestStore->name }}">
          <p>{{ $user->latestStore->name }}（{{ $user->name }}さん）</p>
        </a>
      </div>
    @endif
  @endforeach
</div>

<div class="latest-pagination-wrapper">
  {{ $users->links() }}
</div>

