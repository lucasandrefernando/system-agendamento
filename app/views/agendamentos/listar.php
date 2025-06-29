<?php $titulo = 'Agendamentos - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-calendar me-2"></i> Agendamentos
    </h1>
    <?php if ($_SESSION['usuario_tipo'] !== 'comum'): ?>
        <a href="/projeto-agendamento/public/agendamentos/criar" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Agendamento
        </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                        <input type="text" id="filtro-data-inicio" class="form-control datepicker" placeholder="Data inicial">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                        <input type="text" id="filtro-data-fim" class="form-control datepicker" placeholder="Data final">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                        <select id="filtro-tipo" class="form-select">
                            <option value="">Todos os tipos</option>
                            <option value="AGENDADO">Agendado</option>
                            <option value="EMERGENCIAL">Emergencial</option>
                            <option value="REAGENDADO">Reagendado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <select id="filtro-empreiteira" class="form-select">
                            <option value="">Todas as empreiteiras</option>
                            <?php
                            $empreiteiras = [];
                            foreach ($agendamentos as $agendamento) {
                                if (!in_array($agendamento['empreiteira'], $empreiteiras)) {
                                    $empreiteiras[] = $agendamento['empreiteira'];
                                    echo '<option value="' . htmlspecialchars($agendamento['empreiteira']) . '">' . htmlspecialchars($agendamento['empreiteira']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($agendamentos)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Nenhum agendamento encontrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="tabela-agendamentos" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Veículo</th>
                            <th>Empreiteira</th>
                            <th>Tipo</th>
                            <th>Data/Hora</th>
                            <th>Identificador</th>
                            <th>Confirmado</th>
                            <th>Criado por</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $agendamento): ?>
                            <tr>
                                <td><?= htmlspecialchars($agendamento['veiculo']) ?></td>
                                <td><?= htmlspecialchars($agendamento['empreiteira']) ?></td>
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
                                    <?= date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])) ?>
                                </td>
                                <td><?= htmlspecialchars($agendamento['identificador']) ?></td>
                                <td>
                                    <?php if ($agendamento['confirmacao']): ?>
                                        <span class="badge bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($agendamento['nome_usuario'] ?? 'N/A') ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($_SESSION['usuario_tipo'] === 'admin' || ($_SESSION['usuario_tipo'] === 'medio' && $agendamento['usuario_id'] == $_SESSION['usuario_id'])): ?>
                                            <a href="/projeto-agendamento/public/agendamentos/editar/<?= $agendamento['id'] ?>" class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                                            <button type="button" class="btn btn-outline-danger btn-excluir"
                                                data-id="<?= $agendamento['id'] ?>"
                                                data-veiculo="<?= htmlspecialchars($agendamento['veiculo']) ?>"
                                                title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Exclusão (oculto) -->
<form id="form-excluir" method="post" action="">
    <!-- Será preenchido via JavaScript -->
</form>

<?php
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inicializa Flatpickr para seletores de data
    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        locale: "pt",
        allowInput: true
    });
    
    // Inicializa DataTables
    var table = $("#tabela-agendamentos").DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
        },
        responsive: true,
        order: [[3, "desc"]], // Ordena por data (decrescente)
        pageLength: 10,
        dom: "<\'row\'<\'col-sm-12 col-md-6\'l><\'col-sm-12 col-md-6\'f>>" +
             "<\'row\'<\'col-sm-12\'tr>>" +
             "<\'row\'<\'col-sm-12 col-md-5\'i><\'col-sm-12 col-md-7\'p>>",
        buttons: [
            {
                extend: "excel",
                text: "<i class=\'fas fa-file-excel\'></i> Excel",
                className: "btn btn-success btn-sm",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: "pdf",
                text: "<i class=\'fas fa-file-pdf\'></i> PDF",
                className: "btn btn-danger btn-sm",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: "print",
                text: "<i class=\'fas fa-print\'></i> Imprimir",
                className: "btn btn-info btn-sm",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ]
    });
    
    // Aplicar filtros personalizados
    $("#filtro-tipo").on("change", function() {
        table.column(2).search($(this).val()).draw();
    });
    
    $("#filtro-empreiteira").on("change", function() {
        table.column(1).search($(this).val()).draw();
    });
    
    // Filtro de data personalizado
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var dataInicio = $("#filtro-data-inicio").val();
            var dataFim = $("#filtro-data-fim").val();
            
            if (dataInicio === "" && dataFim === "") {
                return true;
            }
            
            var dataAgendamento = data[3]; // Coluna da data
            
            // Converter para formato de data
            var partes = dataAgendamento.split("/");
            var dataAgendamentoObj = new Date(partes[2].split(" ")[0], partes[1] - 1, partes[0]);
            
            if (dataInicio !== "") {
                var partesInicio = dataInicio.split("/");
                var dataInicioObj = new Date(partesInicio[2], partesInicio[1] - 1, partesInicio[0]);
                
                if (dataAgendamentoObj < dataInicioObj) {
                    return false;
                }
            }
            
            if (dataFim !== "") {
                var partesFim = dataFim.split("/");
                var dataFimObj = new Date(partesFim[2], partesFim[1] - 1, partesFim[0]);
                
                if (dataAgendamentoObj > dataFimObj) {
                    return false;
                }
            }
            
            return true;
        }
    );
    
    // Reaplica os filtros quando as datas mudam
    $("#filtro-data-inicio, #filtro-data-fim").on("change", function() {
        table.draw();
    });
    
    // Configuração dos botões de exclusão
    document.querySelectorAll(".btn-excluir").forEach(function(button) {
        button.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const veiculo = this.getAttribute("data-veiculo");
            
            Swal.fire({
                title: "Confirmar exclusão?",
                text: "Deseja excluir o agendamento do veículo " + veiculo + "? Esta ação não pode ser desfeita.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Sim, excluir",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById("form-excluir");
                    form.action = "/projeto-agendamento/public/agendamentos/excluir/" + id;
                    form.submit();
                }
            });
        });
    });
});
</script>';
?>