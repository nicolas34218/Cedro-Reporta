<form action="{{ route('logout') }}" method="post" class="component-logout-form">
    @csrf
    <button type="submit" class="component-logout-btn" aria-label="Sair">
        <span class="icon">🚪</span>
        <span class="text">Sair</span>
    </button>
</form>