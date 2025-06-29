<?php $titulo = 'Dashboard - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
    </h1>
    <?php if ($_SESSION['usuario_tipo'] !== 'comum'): ?>
        <a href="/projeto-agendamento/public/agendamentos/criar" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Agendamento
        </a>
    <?php endif; ?>
</div>

<!-- Resumo de Indicadores -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total de Agendamentos</h6>
                        <h2 class="mt-2 mb-0"><?= $total_agendamentos ?? 0 ?></h2>
                    </div>
                    <div class="dashboard-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>Atualizado em tempo real</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Taxa de Confirmação</h6>
                        <h2 class="mt-2 mb-0"><?= number_format($taxa_confirmacao['taxa'] ?? 0, 1) ?>%</h2>
                    </div>
                    <div class="dashboard-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small><?= ($taxa_confirmacao['confirmados'] ?? 0) ?> de <?= ($taxa_confirmacao['total'] ?? 0) ?> confirmados</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Agendamentos Hoje</h6>
                        <h2 class="mt-2 mb-0"><?= $agendamentos_hoje ?? 0 ?></h2>
                    </div>
                    <div class="dashboard-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small><?= date('d/m/Y') ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card dashboard-card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Próximos 7 Dias</h6>
                        <h2 class="mt-2 mb-0"><?= $agendamentos_semana ?? 0 ?></h2>
                    </div>
                    <div class="dashboard-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>Próxima semana</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribuição por Tipo -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
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

    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i> Agendamentos por Dia da Semana
                </h5>
            </div>
            <div class="card-body">
                <canvas id="grafico-dias-semana" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Calendário e Horários Disponíveis -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
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
                <div id="calendario-agendamentos"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
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
                        <a href="/projeto-agendamento/public/agendamentos/criar" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Agendar nesta data
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Distribuição por Hora e Últimos Agendamentos -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i> Agendamentos por Hora
                </h5>
            </div>
            <div class="card-body">
                <canvas id="grafico-horas" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i> Últimos Agendamentos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($ultimos_agendamentos)): ?>
                    <p class="text-center text-muted my-5">Nenhum agendamento registrado</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Veículo</th>
                                    <th>Empreiteira</th>
                                    <th>Data/Hora</th>
                                    <th>Tipo</th>
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
                                            <a href="/projeto-agendamento/public/agendamentos/editar/<?= $agendamento['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="/projeto-agendamento/public/agendamentos" class="btn btn-sm btn-outline-secondary">
                            Ver todos os agendamentos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Distribuição por Mês -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2"></i> Tendência de Agendamentos (Últimos 12 Meses)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="grafico-meses" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
$scripts = '
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/pt-br.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Console log para debug
    console.log("Dashboard inicializado");
    
    // Inicializa Flatpickr para seleção de data
    const datePicker = flatpickr("#data_disponibilidade", {
        dateFormat: "Y-m-d",
        locale: "pt",
        allowInput: true,
        defaultDate: "today",
        disable: [
            function(date) {
                // Desabilita finais de semana
                return (date.getDay() === 0 || date.getDay() === 6);
            }
        ],
        onChange: function(selectedDates, dateStr) {
            carregarHorariosDisponiveis(dateStr);
        }
    });
    
    // Carrega horários disponíveis para a data atual
    carregarHorariosDisponiveis(document.getElementById("data_disponibilidade").value);
    
    // Função para carregar horários disponíveis
    function carregarHorariosDisponiveis(data) {
        console.log("Carregando horários para a data:", data);
        
        // Exibir loading
        document.getElementById("horarios-disponiveis-dashboard").innerHTML = \'<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2">Carregando horários disponíveis...</p></div>\';
        
        // Fazer requisição AJAX
        const url = "/projeto-agendamento/public/dashboard/horarios-disponiveis?data=" + data;
        console.log("URL da requisição:", url);
        
        fetch(url)
            .then(response => {
                console.log("Status da resposta:", response.status);
                if (!response.ok) {
                    throw new Error("Erro na requisição: " + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dados recebidos:", data);
                
                let html = \'<div class="disponibilidade-horarios">\';
                
                if (data.horarios && data.horarios.length > 0) {
                    // Agrupar por período (manhã, tarde)
                    const manha = data.horarios.filter(h => {
                        const hora = parseInt(h.horario.split(":")[0]);
                        return hora < 12;
                    });
                    
                    const tarde = data.horarios.filter(h => {
                        const hora = parseInt(h.horario.split(":")[0]);
                        return hora >= 12;
                    });
                    
                    // Exibir horários da manhã
                    html += \'<h6 class="text-muted mb-2"><i class="fas fa-sun me-1"></i> Manhã</h6>\';
                    html += \'<div class="row g-2 mb-3">\';
                    
                    manha.forEach(horario => {
                        const disponivel = horario.disponivel;
                        const classe = disponivel ? "btn-outline-success" : "btn-outline-secondary";
                        const icon = disponivel ? "check" : "times";
                        const disabled = disponivel ? "" : "disabled";
                        
                        html += \'<div class="col-4">\';
                        html += \'<button type="button" class="btn \' + classe + \' w-100 btn-sm \' + disabled + \'" \' + disabled + \'>\';
                        html += \'<i class="fas fa-\' + icon + \' me-1"></i> \' + horario.horario;
                        html += \'</button>\';
                        html += \'</div>\';
                    });
                    
                    html += \'</div>\';
                    
                    // Exibir horários da tarde
                    html += \'<h6 class="text-muted mb-2"><i class="fas fa-moon me-1"></i> Tarde</h6>\';
                    html += \'<div class="row g-2">\';
                    
                    tarde.forEach(horario => {
                        const disponivel = horario.disponivel;
                        const classe = disponivel ? "btn-outline-success" : "btn-outline-secondary";
                        const icon = disponivel ? "check" : "times";
                        const disabled = disponivel ? "" : "disabled";
                        
                        html += \'<div class="col-4">\';
                        html += \'<button type="button" class="btn \' + classe + \' w-100 btn-sm \' + disabled + \'" \' + disabled + \'>\';
                        html += \'<i class="fas fa-\' + icon + \' me-1"></i> \' + horario.horario;
                        html += \'</button>\';
                        html += \'</div>\';
                    });
                    
                    html += \'</div>\';
                    
                    // Resumo de disponibilidade
                    const totalDisponiveis = data.horarios.filter(h => h.disponivel).length;
                    const totalHorarios = data.horarios.length;
                    const percentualDisponivel = Math.round((totalDisponiveis / totalHorarios) * 100);
                    
                    html += \'<div class="mt-3">\';
                    html += \'<div class="d-flex justify-content-between align-items-center mb-1">\';
                    html += \'<span>Disponibilidade</span>\';
                    html += \'<span>\' + totalDisponiveis + \' de \' + totalHorarios + \' horários</span>\';
                    html += \'</div>\';
                    html += \'<div class="progress" style="height: 10px;">\';
                    html += \'<div class="progress-bar bg-success" role="progressbar" style="width: \' + percentualDisponivel + \'%;" aria-valuenow="\' + percentualDisponivel + \'" aria-valuemin="0" aria-valuemax="100"></div>\';
                    html += \'</div>\';
                    html += \'<div class="text-center mt-1"><small class="text-muted">\' + percentualDisponivel + \'% disponível</small></div>\';
                    html += \'</div>\';
                } else {
                    html = \'<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i> Nenhum horário disponível para esta data.</div>\';
                }
                
                html += \'</div>\';
                document.getElementById("horarios-disponiveis-dashboard").innerHTML = html;
            })
            .catch(error => {
                console.error("Erro ao consultar horários:", error);
                document.getElementById("horarios-disponiveis-dashboard").innerHTML = \'<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao consultar horários disponíveis: \' + error.message + \'</div>\';
            });
    }
    
    // Inicializa o calendário
    const calendarEl = document.getElementById("calendario-agendamentos");
    if (calendarEl) {
        console.log("Inicializando calendário");
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            locale: "pt-br",
            headerToolbar: {
                left: "",
                center: "title",
                right: ""
            },
            buttonText: {
                today: "Hoje"
            },
            dayMaxEvents: true,
            events: ' . json_encode($eventos_calendario ?? []) . ',
            eventClick: function(info) {
                // Ao clicar em um evento, mostrar detalhes
                const evento = info.event;
                const dataHora = new Date(evento.start);
                
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        title: evento.title,
                        html: `
                            <div class="text-start">
                                <p><strong>Data/Hora:</strong> ${dataHora.toLocaleString("pt-BR")}</p>
                                <p><strong>Empreiteira:</strong> ${evento.extendedProps.empreiteira || "N/A"}</p>
                                <p><strong>Tipo:</strong> ${evento.extendedProps.tipo || "N/A"}</p>
                                <p><strong>Identificador:</strong> ${evento.extendedProps.identificador || "N/A"}</p>
                                <p><strong>Confirmado:</strong> ${evento.extendedProps.confirmado ? "Sim" : "Não"}</p>
                            </div>
                        `,
                        icon: "info",
                        confirmButtonText: "Fechar"
                    });
                } else {
                    alert(`Detalhes do Agendamento:\nVeículo: ${evento.title}\nData/Hora: ${dataHora.toLocaleString("pt-BR")}\nEmpreiteira: ${evento.extendedProps.empreiteira || "N/A"}\nTipo: ${evento.extendedProps.tipo || "N/A"}`);
                }
            },
            dateClick: function(info) {
                // Ao clicar em uma data, mostrar horários disponíveis
                datePicker.setDate(info.dateStr);
                carregarHorariosDisponiveis(info.dateStr);
                
                // Rolar para a seção de horários disponíveis
                document.getElementById("horarios-disponiveis-dashboard").scrollIntoView({ behavior: "smooth" });
            }
        });
        calendar.render();
        
        // Botões de navegação do calendário
        const btnMesAnterior = document.getElementById("btn-mes-anterior");
        const btnMesAtual = document.getElementById("btn-mes-atual");
        const btnMesProximo = document.getElementById("btn-mes-proximo");
        
        if (btnMesAnterior) {
            btnMesAnterior.addEventListener("click", function() {
                calendar.prev();
            });
        }
        
        if (btnMesAtual) {
            btnMesAtual.addEventListener("click", function() {
                calendar.today();
            });
        }
        
        if (btnMesProximo) {
            btnMesProximo.addEventListener("click", function() {
                calendar.next();
            });
        }
    } else {
        console.error("Elemento calendario-agendamentos não encontrado");
    }
    
    // Gráfico de tipos de agendamento
    const ctxTipos = document.getElementById("grafico-tipos");
    if (ctxTipos) {
        console.log("Inicializando gráfico de tipos");
        console.log("Labels:", ' . json_encode($grafico_tipos['labels'] ?? []) . ');
        console.log("Values:", ' . json_encode($grafico_tipos['values'] ?? []) . ');
        
        new Chart(ctxTipos, {
            type: "doughnut",
            data: {
                labels: ' . json_encode($grafico_tipos['labels'] ?? []) . ',
                datasets: [{
                    data: ' . json_encode($grafico_tipos['values'] ?? []) . ',
                    backgroundColor: ' . json_encode($grafico_tipos['backgroundColors'] ?? []) . ',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });
    } else {
        console.error("Elemento grafico-tipos não encontrado");
    }
    
    // Gráfico de agendamentos por dia da semana
    const ctxDiasSemana = document.getElementById("grafico-dias-semana");
    if (ctxDiasSemana) {
        console.log("Inicializando gráfico de dias da semana");
        console.log("Labels:", ' . json_encode($grafico_dias_semana['labels'] ?? []) . ');
        console.log("Values:", ' . json_encode($grafico_dias_semana['values'] ?? []) . ');
        
        new Chart(ctxDiasSemana, {
            type: "bar",
            data: {
                labels: ' . json_encode($grafico_dias_semana['labels'] ?? []) . ',
                datasets: [{
                    label: "Agendamentos",
                    data: ' . json_encode($grafico_dias_semana['values'] ?? []) . ',
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    } else {
        console.error("Elemento grafico-dias-semana não encontrado");
    }
    
    // Gráfico de agendamentos por hora
    const ctxHoras = document.getElementById("grafico-horas");
    if (ctxHoras) {
        console.log("Inicializando gráfico de horas");
        console.log("Labels:", ' . json_encode($grafico_horas['labels'] ?? []) . ');
        console.log("Values:", ' . json_encode($grafico_horas['values'] ?? []) . ');
        
        new Chart(ctxHoras, {
            type: "line",
            data: {
                labels: ' . json_encode($grafico_horas['labels'] ?? []) . ',
                datasets: [{
                    label: "Agendamentos",
                    data: ' . json_encode($grafico_horas['values'] ?? []) . ',
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    } else {
        console.error("Elemento grafico-horas não encontrado");
    }
    
    // Gráfico de agendamentos por mês
    const ctxMeses = document.getElementById("grafico-meses");
    if (ctxMeses) {
        console.log("Inicializando gráfico de meses");
        console.log("Labels:", ' . json_encode($grafico_meses['labels'] ?? []) . ');
        console.log("Values:", ' . json_encode($grafico_meses['values'] ?? []) . ');
        
        new Chart(ctxMeses, {
            type: "bar",
            data: {
                labels: ' . json_encode($grafico_meses['labels'] ?? []) . ',
                datasets: [{
                    label: "Agendamentos",
                    data: ' . json_encode($grafico_meses['values'] ?? []) . ',
                    backgroundColor: "rgba(153, 102, 255, 0.7)",
                    borderColor: "rgba(153, 102, 255, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    } else {
        console.error("Elemento grafico-meses não encontrado");
    }
});
</script>

<style>
.disponibilidade-horarios .btn {
    margin-bottom: 5px;
    font-size: 0.85rem;
}
.fc-daygrid-day.fc-day-today {
    background-color: rgba(13, 110, 253, 0.1) !important;
}
.fc-event {
    cursor: pointer;
}
</style>';
?>