<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Secretária - Cedro Reporta</title>
    <link rel="stylesheet" href="{{ asset('css/secretary-create.css') }}">
</head>
<body>
    <div class="secretary-page">
        <header class="secretary-header">
            <h1>Cadastro de Secretaria</h1>
            <p>Cadastre as secretarias responsáveis pelo atendimento das denúncias</p>
        </header>

        <section class="secretary-grid">
            <div class="secretary-card secretary-form-card">
                <h2 class="card-title">NOVA SECRETARIA</h2>

                <form action="{{ route('secretary.store') }}" method="POST" class="secretary-form">
                    @csrf

                    <div class="field">
                        <label for="name">Nome da secretaria</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}">
                        @error('name')
                            <small class="error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <small class="error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="category">Categoria</label>
                        <select id="category" name="category">
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <small class="error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password">
                        @error('password')
                            <small class="error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirmar senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation">
                        @error('password_confirmation')
                            <small class="error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">Cadastrar Secretaria</button>
                </form>
            </div>

            <div class="secretary-list-column">
                <h2 class="list-title">SECRETARIAS EXISTENTES</h2>

                <div class="secretary-list">
                    @forelse ($secretaries as $secretary)
                        <article class="secretary-item">
                            <strong>{{ $secretary->name }}</strong>
                            <p>{{ $secretary->category }}</p>
                        </article>
                    @empty
                        <article class="secretary-item empty-state">
                            <strong>Nenhuma secretaria cadastrada ainda.</strong>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</body>
</html>
