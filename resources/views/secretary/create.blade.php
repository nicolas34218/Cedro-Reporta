<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Secretária - Cedro Reporta</title>
</head>
<body>
    <h1>Cadastrar Nova Secretária</h1>

    @if ($errors->any())
        <div>
            <h3>Erros na validação:</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('secretary.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nome da Secretária:</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="category">Categoria:</label>
            <select id="category" name="category" required>
                <option value="">-- Selecione uma categoria --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            @error('category')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirmar Senha:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            @error('password_confirmation')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <button type="submit">Criar Secretária</button>
            <a href="{{ route('admin.dashboard') }}">
                <button type="button">Cancelar</button>
            </a>
        </div>
    </form>
</body>
</html>
