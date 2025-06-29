<?php $titulo = 'Dashboard - Sistema de Agendamento'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h2 class="h4 mb-3">
                    <i class="fas fa-tachometer-alt me-2"></i> Painel de Controle
                </h2>
                <p class="mb-0">Bem-vindo ao sistema de agendamento de veículos. Use o menu acima para navegar.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i> Próximos Agendamentos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($agendamentos)): ?>
                    <p class="text-center py-3">
                        <i class="fas fa-info-circle me-1"></i> Nenhum agendamento encontrado
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Veículo</th>
                                    <th>Empreiteira</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agendamento['veiculo']) ?></td>
                                        <td><?= htmlspecialchars($agendamento['empreiteira']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusIcon = '';

                                            switch ($agendamento['tipo']) {
                                                case 'AGENDADO':
                                                    $statusClass = 'success';
                                                    $statusIcon = 'calendar-check';
                                                    break;
                                                case 'EMERGENCIAL':
                                                    $statusClass = 'danger';
                                                    $statusIcon = 'exclamation-triangle';
                                                    break;
                                                case 'REAGENDADO':
                                                    $statusClass = 'warning';
                                                    $statusIcon = 'calendar-plus';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <i class="fas fa-<?= $statusIcon ?> me-1"></i>
                                                <?= $agendamento['tipo'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])) ?></td>
                                        <td>
                                            <a href="/projeto-agendamento/public/agendamentos/editar/<?= $agendamento['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="/projeto-agendamento/public/agendamentos/criar" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Criar novo agendamento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Acesso Rápido
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="/projeto-agendamento/public/agendamentos/criar" class="btn btn-lg btn-success">
                        <i class="fas fa-plus-circle me-2"></i> Novo Agendamento
                    </a>

                    <a href="/projeto-agendamento/public/agendamentos" class="btn btn-lg btn-primary">
                        <i class="fas fa-calendar me-2"></i> Ver Agendamentos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>