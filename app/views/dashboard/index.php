<?php $titulo = 'Dashboard - Sistema de Agendamento'; ?>

<!-- Cabeçalho com Filtro de Data -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-lg-6">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </h1>
                <p class="text-muted mb-0" id="filtro-atual">Visão geral dos agendamentos</p>
            </div>
            <div class="col-lg-6 text-lg-end">
                <span class="badge bg-primary-soft me-2">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <span id="periodo-personalizado-texto">
                        <?= date('d/m/Y', strtotime('first day of this month')) ?> até <?= date('d/m/Y', strtotime('last day of this month')) ?>
                    </span>
                </span>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtroCollapse" aria-expanded="false" aria-controls="filtroCollapse">
                    <i class="fas fa-filter me-1"></i> Filtros
                </button>
            </div>
        </div>

        <div class="collapse" id="filtroCollapse">
            <div class="filtro-container p-3 border rounded">
                <div class="row">
                    <!-- Filtros Rápidos -->
                    <div class="col-md-6 mb-3">
                        <h6 class="filtro-heading mb-2">
                            <i class="fas fa-bolt me-1"></i> Filtros rápidos
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="btn-periodo-hoje" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar-day me-1"></i> Hoje
                            </button>
                            <button type="button" id="btn-periodo-semana" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar-week me-1"></i> Esta Semana
                            </button>
                            <button type="button" id="btn-periodo-mes" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar-alt me-1"></i> Este Mês
                            </button>
                            <button type="button" id="btn-periodo-ano" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar me-1"></i> Este Ano
                            </button>
                        </div>
                    </div>

                    <!-- Filtro Personalizado -->
                    <div class="col-md-6 mb-3">
                        <h6 class="filtro-heading mb-2">
                            <i class="fas fa-calendar-alt me-1"></i> Período personalizado
                        </h6>
                        <div class="row g-2">
                            <div class="col-sm-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="text" id="filtro-data-inicio" class="form-control datepicker" placeholder="Data inicial" value="<?= date('d/m/Y', strtotime('first day of this month')) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2 text-center">
                                <div class="filtro-separador">até</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="text" id="filtro-data-fim" class="form-control datepicker" placeholder="Data final" value="<?= date('d/m/Y', strtotime('last day of this month')) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-2">
                    <button id="btn-limpar-filtro" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> Limpar
                    </button>
                    <button id="btn-aplicar-filtro" class="btn btn-sm btn-primary">
                        <i class="fas fa-check me-1"></i> Aplicar Filtro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navegação em Abas -->
<ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-tab-pane" type="button" role="tab">
            <i class="fas fa-calendar-alt me-1"></i> Calendário
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats-tab-pane" type="button" role="tab">
            <i class="fas fa-chart-bar me-1"></i> Estatísticas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent-tab-pane" type="button" role="tab">
            <i class="fas fa-history me-1"></i> Recentes
        </button>
    </li>
</ul>

<!-- Conteúdo das Abas -->
<div class="tab-content" id="dashboardTabsContent">
    <!-- Aba do Calendário -->
    <div class="tab-pane fade show active" id="calendar-tab-pane" role="tabpanel" tabindex="0">
        <div class="row">
            <!-- Calendário (Maior) -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Calendário de Agendamentos
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" id="btn-mes-anterior" class="btn btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" id="btn-mes-atual" class="btn btn-outline-primary">Hoje</button>
                            <button type="button" id="btn-mes-proximo" class="btn btn-outline-secondary">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendario-agendamentos" style="height: 600px;"></div>
                    </div>
                </div>
            </div>

            <!-- Horários Disponíveis e Resumo -->
            <div class="col-lg-4 mb-4">
                <!-- Resumo de Indicadores -->
                <div class="row mb-4">
                    <div class="col-6 mb-3">
                        <div class="card dashboard-card bg-primary text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0 small">Total</h6>
                                        <h3 class="mt-1 mb-0" id="total-agendamentos"><?= $total_agendamentos ?? 0 ?></h3>
                                    </div>
                                    <div class="dashboard-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 mb-3">
                        <div class="card dashboard-card bg-success text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0 small">Confirmados</h6>
                                        <h3 class="mt-1 mb-0" id="taxa-confirmacao"><?= number_format($taxa_confirmacao['taxa'] ?? 0, 0) ?>%</h3>
                                    </div>
                                    <div class="dashboard-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 mb-3">
                        <div class="card dashboard-card bg-warning text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0 small">Hoje</h6>
                                        <h3 class="mt-1 mb-0" id="agendamentos-hoje"><?= $agendamentos_hoje ?? 0 ?></h3>
                                    </div>
                                    <div class="dashboard-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 mb-3">
                        <div class="card dashboard-card bg-info text-white h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-0 small">Próx. 7 dias</h6>
                                        <h3 class="mt-1 mb-0" id="agendamentos-semana"><?= $agendamentos_semana ?? 0 ?></h3>
                                    </div>
                                    <div class="dashboard-icon">
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horários Disponíveis -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i> Horários Disponíveis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="data_disponibilidade" class="form-label">Selecione uma data</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                <input type="text" id="data_disponibilidade" class="form-control datepicker" placeholder="Selecione uma data" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div id="horarios-disponiveis-dashboard" class="mt-3">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2">Carregando horários disponíveis...</p>
                            </div>
                        </div>

                        <?php if ($_SESSION['usuario_tipo'] !== 'comum'): ?>
                            <div class="mt-3 text-center">
                                <a href="<?= url('agendamentos/criar') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Agendar nesta data
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aba de Estatísticas -->
    <div class="tab-pane fade" id="stats-tab-pane" role="tabpanel" tabindex="0">
        <div class="row">
            <!-- Distribuição por Tipo -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i> Agendamentos por Tipo
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($agendamentos_por_tipo)): ?>
                            <p class="text-center text-muted my-5">Nenhum dado disponível</p>
                        <?php else: ?>
                            <div class="row">
                                <?php
                                $total = 0;
                                foreach ($agendamentos_por_tipo as $item) {
                                    $total += $item['total'] ?? 0;
                                }

                                $tipoClasses = [
                                    'AGENDADO' => 'success',
                                    'EMERGENCIAL' => 'danger',
                                    'REAGENDADO' => 'warning'
                                ];

                                foreach ($agendamentos_por_tipo as $item):
                                    $tipo = $item['tipo'] ?? '';
                                    $quantidade = $item['total'] ?? 0;
                                    $percentual = $total > 0 ? round(($quantidade / $total) * 100) : 0;
                                    $classe = $tipoClasses[$tipo] ?? 'secondary';
                                ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center">
                                            <h3 class="mb-0"><?= $quantidade ?></h3>
                                            <div class="text-muted mb-2"><?= $tipo ?></div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-<?= $classe ?>" role="progressbar"
                                                    style="width: <?= $percentual ?>%;"
                                                    aria-valuenow="<?= $percentual ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted"><?= $percentual ?>% do total</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-3">
                                <canvas id="grafico-tipos" style="height: 250px; width: 100%;"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Agendamentos por Dia da Semana -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i> Agendamentos por Dia da Semana
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="grafico-dias-semana" style="height: 250px; max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Agendamentos por Hora -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i> Agendamentos por Hora
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="grafico-horas" style="height: 250px; max-height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tendência de Agendamentos -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area me-2"></i> Tendência de Agendamentos
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="grafico-meses" style="height: 250px; max-height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aba de Agendamentos Recentes -->
    <div class="tab-pane fade" id="recent-tab-pane" role="tabpanel" tabindex="0">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i> Últimos Agendamentos
                </h5>
                <?php if ($_SESSION['usuario_tipo'] !== 'comum'): ?>
                    <a href="<?= url('agendamentos/criar') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Novo Agendamento
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($ultimos_agendamentos)): ?>
                    <p class="text-center text-muted my-5">Nenhum agendamento registrado</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Veículo</th>
                                    <th>Empreiteira</th>
                                    <th>Data/Hora</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_agendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agendamento['veiculo']) ?></td>
                                        <td><?= htmlspecialchars($agendamento['empreiteira']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])) ?></td>
                                        <td>
                                            <?php
                                            $tipoClasses = [
                                                'AGENDADO' => 'success',
                                                'EMERGENCIAL' => 'danger',
                                                'REAGENDADO' => 'warning'
                                            ];
                                            $tipoClass = $tipoClasses[$agendamento['tipo']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $tipoClass ?>">
                                                <?= htmlspecialchars($agendamento['tipo']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $agendamento['confirmacao'] ? 'success' : 'secondary' ?>">
                                                <?= $agendamento['confirmacao'] ? 'Confirmado' : 'Pendente' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= url('agendamentos/editar/' . $agendamento['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= url('agendamentos') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i> Ver todos os agendamentos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// No final do arquivo
$scripts = '
<link href="/assets/css/dashboard.css" rel="stylesheet">
<script>
const dadosDashboard = ' . json_encode($dadosJS) . ';
</script>
<script src="/assets/js/dashboard.js"></script>
';
?>