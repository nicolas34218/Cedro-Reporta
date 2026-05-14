<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Secretária - Cedro Reporta</title>
</head>
<body>
    <h1>Dashboard da Secretária</h1>

    <div>
        <h2>Bem-vindo, {{ auth()->user()->name }}!</h2>
        <p><strong>Categoria:</strong> {{ $category }}</p>
    </div>

    <!-- Estatísticas -->
    <section>
        <h2>Estatísticas de Denúncias - Categoria: {{ $category }}</h2>
        <div>
            <div>
                <h3>Total de Denúncias</h3>
                <p>{{ $statistics['total_reports'] }}</p>
            </div>
            <div>
                <h3>Pendentes</h3>
                <p>{{ $statistics['pending_reports'] }}</p>
            </div>
            <div>
                <h3>Em Análise</h3>
                <p>{{ $statistics['analyzing_reports'] }}</p>
            </div>
            <div>
                <h3>Resolvidas</h3>
                <p>{{ $statistics['resolved_reports'] }}</p>
            </div>
        </div>
    </section>

    <!-- Denúncias da Categoria -->
    <section>
        <h2>Denúncias da Categoria: {{ $category }}</h2>

        @if ($reports->isEmpty())
            <p>Nenhuma denúncia cadastrada para esta categoria.</p>
        @else
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Denunciante</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Localização</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->title }}</td>
                            <td>{{ Str::limit($report->description, 50) }}</td>
                            <td>{{ $report->citizen->name ?? 'Desconhecido' }}</td>
                            <td>{{ $report->category }}</td>
                            <td>{{ $report->status }}</td>
                            <td>{{ $report->location ?? 'Não informado' }}</td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    <!-- Menu -->
    <section>
        <h3>Menu</h3>
        <form action="{{ route('logout') }}" method="post" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </section>
</body>
</html>
