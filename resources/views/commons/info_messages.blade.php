@if (session('info'))
  <div class="info-message">
    <span class="info-icon">🍀</span>
    <span class="info-text">{{ session('info') }}</span>
  </div>
@endif
