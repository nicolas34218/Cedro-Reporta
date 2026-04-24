<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Cedro Reporta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-section">
                <div class="logo">⚠</div>
                <h1>Cedro<span>Reporta</span></h1>
                <p class="user-type">Administrador</p>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item active">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-item">
                    <span class="icon">📋</span>
                    <span>Denúncias</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="icon">👥</span>
                    <span>Usuários</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="icon">⚙️</span>
                    <span>Configurações</span>
                </a>
            </nav>

            <!-- Botão de Logout -->
            <form action="{{ route('logout') }}" method="post" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <span class="icon">🚪</span>
                    <span>Sair</span>
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="header-left">
                    <h2>Dashboard</h2>
                    <p class="date">{{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="header-right">
                    <span class="user-greeting">Bem-vindo, {{ auth()->user()->name }}!</span>
                </div>
            </header>

            <!-- Statistics Cards -->
            <section class="statistics">
                <div class="stat-card">
                    <h3>TOTAL DE DENÚNCIAS</h3>
                    <p class="stat-number">{{ $statistics['total_reports'] }}</p>
                    <small>{{ $statistics['total_reports'] > 0 ? 'Ativas' : 'Nenhuma' }}</small>
                </div>

                <div class="stat-card pending">
                    <h3>PENDENTES</h3>
                    <p class="stat-number">{{ $statistics['open_reports'] }}</p>
                    <small>{{ number_format(($statistics['open_reports'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
                </div>

                <div class="stat-card in-analysis">
                    <h3>EM ANDAMENTO</h3>
                    <p class="stat-number">{{ $statistics['in_analysis'] }}</p>
                    <small>{{ number_format(($statistics['in_analysis'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
                </div>

                <div class="stat-card resolved">
                    <h3>RESOLVIDAS</h3>
                    <p class="stat-number">{{ $statistics['resolved'] }}</p>
                    <small>{{ number_format(($statistics['resolved'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
                </div>
            </section>

            <!-- Reports Management Section -->
            <section class="reports-section">
                <div class="section-header">
                    <h3>Gerenciamento de Denúncias</h3>
                    <div class="filters">
                        <input type="text" class="search-box" placeholder="Buscar...">
                        <select class="filter-select">
                            <option>Todos os status</option>
                            <option>Aberta</option>
                            <option>Em Análise</option>
                            <option>Resolvida</option>
                            <option>Fechada</option>
                        </select>
                        <select class="filter-select">
                            <option>Todas as categorias</option>
                            <option>Iluminação</option>
                            <option>Buracos</option>
                            <option>Lixo</option>
                            <option>Outros</option>
                        </select>
                    </div>
                </div>

                <!-- Reports Table -->
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TÍTULO</th>
                            <th>CATEGORIA</th>
                            <th>BAIRRO</th>
                            <th>DATA</th>
                            <th>STATUS</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReports as $report)
                            <tr>
                                <td>#{{ $report->id }}</td>
                                <td>{{ substr($report->title, 0, 40) }}...</td>
                                <td>
                                    <span class="category-badge">{{ $report->category }}</span>
                                </td>
                                <td>{{ $report->district ?? 'Sem bairro' }}</td>
                                <td>{{ $report->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="action-btn">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Nenhuma denúncia encontrada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-info">
                    Exibindo 5 de {{ $statistics['total_reports'] }} registros
                </div>
            </section>
        </main>
    </div>
</body>
</html>