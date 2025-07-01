<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta tags para funcionalidades do dashboard -->
    <meta name="user-role" content="<?= $_SESSION['usuario_tipo'] ?? 'user' ?>">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <title><?= $titulo ?? 'Sistema de Agendamento de Veículos' ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?= url('assets/favicon.ico') ?>" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Flatpickr (Seletor de data/hora) -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" rel="stylesheet">

    <!-- FullCalendar (para o dashboard) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="<?= rtrim(env('APP_URL', 'https://chamados.anacron.com.br'), '/') ?>/assets/css/dashboard.css" rel="stylesheet">


    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .content {
            flex: 1;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .footer {
            padding: 1rem 0;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1rem 1.25rem;
        }

        .btn {
            border-radius: 0.25rem;
        }

        .table th {
            font-weight: 600;
        }

        .badge {
            font-weight: 500;
        }

        .dashboard-card {
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        /* Estilos para o header */
        .navbar {
            padding: 0.75rem 1rem;
        }

        .navbar-brand {
            font-size: 1.25rem;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .dropdown-item:active,
        .dropdown-item:focus {
            background-color: #f8f9fa;
            color: #212529;
        }

        .dropdown-item:hover {
            background-color: #e9ecef;
        }

        .dropdown-item i {
            width: 1.25rem;
            text-align: center;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Avatar do usuário */
        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Animação para os dropdowns */
        .animate {
            animation-duration: 0.3s;
            animation-fill-mode: both;
        }

        @keyframes slideIn {
            0% {
                transform: translateY(1rem);
                opacity: 0;
            }

            100% {
                transform: translateY(0rem);
                opacity: 1;
            }
        }

        .slideIn {
            animation-name: slideIn;
        }

        /* Destaque para item ativo no menu */
        .navbar-dark .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.25rem;
        }

        /* Ajustes para mobile */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding: 1rem 0;
            }

            .navbar-nav .dropdown-menu {
                box-shadow: none;
                border-left: 3px solid #0d6efd;
                border-radius: 0;
                background-color: rgba(0, 0, 0, 0.05);
                margin-left: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header/Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <!-- Logo e Título -->
                <a class="navbar-brand d-flex align-items-center" href="<?= url('dashboard') ?>">
                    <i class="fas fa-calendar-alt me-2 fs-3"></i>
                    <span class="fw-bold">Agendamento de Veículos</span>
                </a>

                <!-- Botão de toggle para mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu principal -->
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($uri, '/dashboard') !== false ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>

                        <!-- Agendamentos -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= strpos($uri, '/agendamentos') !== false ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar-check me-1"></i> Agendamentos
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                <li>
                                    <a class="dropdown-item" href="<?= url('agendamentos') ?>">
                                        <i class="fas fa-list me-2"></i> Listar Todos
                                    </a>
                                </li>
                                <?php if ($_SESSION['usuario_tipo'] !== 'comum'): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('agendamentos/criar') ?>">
                                            <i class="fas fa-plus me-2"></i> Novo Agendamento
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('agendamentos?tipo=AGENDADO') ?>">
                                        <i class="fas fa-check-circle me-2 text-success"></i> Agendados
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('agendamentos?tipo=EMERGENCIAL') ?>">
                                        <i class="fas fa-exclamation-circle me-2 text-danger"></i> Emergenciais
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= url('agendamentos?tipo=REAGENDADO') ?>">
                                        <i class="fas fa-sync me-2 text-warning"></i> Reagendados
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Administração (apenas para admin) -->
                        <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?= strpos($uri, '/admin') !== false || strpos($uri, '/usuarios') !== false ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Administração
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                    <li>
                                        <a class="dropdown-item" href="<?= url('usuarios') ?>">
                                            <i class="fas fa-users me-2"></i> Usuários
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('usuarios/criar') ?>">
                                            <i class="fas fa-user-plus me-2"></i> Novo Usuário
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Menu do usuário -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="avatar-circle bg-white text-primary me-2">
                                    <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate slideIn">
                                <li>
                                    <div class="dropdown-item-text">
                                        <div class="fw-bold"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></div>
                                        <div class="small text-muted">
                                            <?php
                                            $tipoNomes = [
                                                'admin' => 'Administrador',
                                                'medio' => 'Operador',
                                                'comum' => 'Visualizador'
                                            ];
                                            echo $tipoNomes[$_SESSION['usuario_tipo'] ?? ''] ?? $_SESSION['usuario_tipo'] ?? 'Usuário';
                                            ?>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= url('logout') ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i> Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Notificações de sistema -->
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] === 'erro' ? 'danger' : 'success' ?> alert-dismissible fade show">
                <i class="fas fa-<?= $_SESSION['mensagem']['tipo'] === 'erro' ? 'exclamation-circle' : 'check-circle' ?> me-2"></i>
                <?= $_SESSION['mensagem']['texto'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <!-- Conteúdo principal -->
    <div class="content">
        <div class="container">
            <?= $conteudo ?? '' ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> Sistema de Agendamento de Veículos
                    </small>
                </div>
                <div>
                    <small class="text-muted">
                        Versão 1.0
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Definir URL base para uso em JavaScript
        const baseUrl = '<?= rtrim(env('APP_URL', 'https://chamados.anacron.com.br'), '/') ?>';
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Flatpickr (Seletor de data/hora) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

    <!-- FullCalendar (para o dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/pt-br.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Scripts personalizados -->
    <script src="<?= rtrim(env('APP_URL', 'https://chamados.anacron.com.br'), '/') ?>/assets/js/dashboard.js"></script>

    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>

</html>