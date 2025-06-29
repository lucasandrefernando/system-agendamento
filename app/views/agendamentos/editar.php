<?php $titulo = 'Editar Agendamento - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-edit me-2"></i> Editar Agendamento
    </h1>
    <a href="/projeto-agendamento/public/agendamentos" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="row">
    <!-- Formulário de Agendamento -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i> Dados do Agendamento
                </h5>
            </div>
            <div class="card-body">
                <form action="/projeto-agendamento/public/agendamentos/editar/<?= $agendamento['id'] ?>" method="post" id="form-agendamento">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="veiculo" class="form-label">Veículo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                <input type="text" class="form-control" id="veiculo" name="veiculo"
                                    value="<?= htmlspecialchars($agendamento['veiculo']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="empreiteira" class="form-label">Empreiteira <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="empreiteira" name="empreiteira"
                                    value="<?= htmlspecialchars($agendamento['empreiteira']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Agendamento <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="AGENDADO" <?= $agendamento['tipo'] === 'AGENDADO' ? 'selected' : '' ?>>Agendado</option>
                                    <option value="EMERGENCIAL" <?= $agendamento['tipo'] === 'EMERGENCIAL' ? 'selected' : '' ?>>Emergencial</option>
                                    <option value="REAGENDADO" <?= $agendamento['tipo'] === 'REAGENDADO' ? 'selected' : '' ?>>Reagendado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="identificador" class="form-label">Identificador <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="identificador" name="identificador"
                                    value="<?= htmlspecialchars($agendamento['identificador']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="data_agendamento" class="form-label">Data e Hora <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="text" class="form-control" id="data_agendamento" name="data_agendamento"
                                    value="<?= date('Y-m-d H:i', strtotime($agendamento['data_agendamento'])) ?>" required readonly>
                            </div>
                            <small class="text-muted">Selecione um horário disponível no painel ao lado</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="documento" class="form-label">Documento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control" id="documento" name="documento"
                                    value="<?= htmlspecialchars($agendamento['documento'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="confirmacao" name="confirmacao" value="1"
                                    <?= $agendamento['confirmacao'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="confirmacao">Agendamento confirmado</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Criado por</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($nome_usuario ?? 'N/A') ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/projeto-agendamento/public/agendamentos" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Atualizar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Painel de Horários Disponíveis -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm sticky-top" style="top: 20px; z-index: 100;">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i> Horários Disponíveis
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="data_consulta" class="form-label">Selecione uma data</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                        <input type="text" id="data_consulta" class="form-control datepicker" placeholder="Selecione uma data" value="<?= date('Y-m-d', strtotime($agendamento['data_agendamento'])) ?>">
                    </div>
                </div>

                <div id="horarios-disponiveis" class="mt-3">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando horários disponíveis...</p>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i> Clique em um horário disponível para selecioná-lo.
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> Alterar o horário pode afetar outros agendamentos. Verifique cuidadosamente.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    const agendamentoId = "' . $agendamento['id'] . '";
    const dataAtual = "' . date('Y-m-d', strtotime($agendamento['data_agendamento'])) . '";
    const horaAtual = "' . date('H:i', strtotime($agendamento['data_agendamento'])) . '";
    
    // Inicializa Flatpickr para consulta de horários
    const datePicker = flatpickr("#data_consulta", {
        dateFormat: "Y-m-d",
        locale: "pt",
        allowInput: true,
        defaultDate: dataAtual,
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
    carregarHorariosDisponiveis(document.getElementById("data_consulta").value);
    
    // Função para carregar horários disponíveis
    function carregarHorariosDisponiveis(data) {
        // Exibir loading
        document.getElementById("horarios-disponiveis").innerHTML = \'<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2">Carregando horários disponíveis...</p></div>\';
        
        // Fazer requisição AJAX
        fetch("/projeto-agendamento/public/dashboard/horarios-disponiveis?data=" + data + "&excluir_id=" + agendamentoId)
            .then(response => response.json())
            .then(data => {
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
                        const isAtual = (data.data === dataAtual && horario.horario === horaAtual);
                        
                        let classe = "btn-outline-secondary";
                        let icon = "times";
                        let disabled = "disabled";
                        
                        if (disponivel) {
                            classe = "btn-outline-success";
                            icon = "check";
                            disabled = "";
                        }
                        
                        if (isAtual) {
                            classe = "btn-success active";
                            icon = "check";
                            disabled = "";
                        }
                        
                        html += \'<div class="col-4">\';
                        html += \'<button type="button" class="btn \' + classe + \' w-100 btn-sm btn-horario \' + disabled + \'" \' + disabled + \' data-horario="\' + horario.horario + \'" data-data="\' + data.data + \'">\';
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
                        const isAtual = (data.data === dataAtual && horario.horario === horaAtual);
                        
                        let classe = "btn-outline-secondary";
                        let icon = "times";
                        let disabled = "disabled";
                        
                        if (disponivel) {
                            classe = "btn-outline-success";
                            icon = "check";
                            disabled = "";
                        }
                        
                        if (isAtual) {
                            classe = "btn-success active";
                            icon = "check";
                            disabled = "";
                        }
                        
                        html += \'<div class="col-4">\';
                        html += \'<button type="button" class="btn \' + classe + \' w-100 btn-sm btn-horario \' + disabled + \'" \' + disabled + \' data-horario="\' + horario.horario + \'" data-data="\' + data.data + \'">\';
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
                document.getElementById("horarios-disponiveis").innerHTML = html;
                
                // Adicionar evento de clique nos botões de horário
                document.querySelectorAll(".btn-horario").forEach(btn => {
                    btn.addEventListener("click", function() {
                        const horario = this.getAttribute("data-horario");
                        const data = this.getAttribute("data-data");
                        const dataCompleta = data + " " + horario;
                        document.getElementById("data_agendamento").value = dataCompleta;
                        
                        // Destacar o botão selecionado
                        document.querySelectorAll(".btn-horario").forEach(b => {
                            b.classList.remove("active");
                            if (b.classList.contains("btn-success") && !b.classList.contains("active")) {
                                b.classList.replace("btn-success", "btn-outline-success");
                            }
                        });
                        this.classList.add("active");
                        this.classList.replace("btn-outline-success", "btn-success");
                        
                        // Mostrar mensagem de confirmação
                        Swal.fire({
                            title: "Horário selecionado!",
                            text: "Você selecionou o dia " + formatarData(data) + " às " + horario + ".",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                });
            })
            .catch(error => {
                console.error("Erro ao consultar horários:", error);
                document.getElementById("horarios-disponiveis").innerHTML = \'<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao consultar horários disponíveis. Tente novamente.</div>\';
            });
    }
    
    // Função para formatar data
    function formatarData(dataStr) {
        const data = new Date(dataStr);
        return data.toLocaleDateString("pt-BR");
    }
    
    // Validação do formulário
    document.getElementById("form-agendamento").addEventListener("submit", function(e) {
        const veiculo = document.getElementById("veiculo").value.trim();
        const empreiteira = document.getElementById("empreiteira").value.trim();
        const identificador = document.getElementById("identificador").value.trim();
        const dataAgendamento = document.getElementById("data_agendamento").value.trim();
        
        if (!veiculo || !empreiteira || !identificador || !dataAgendamento) {
            e.preventDefault();
            
            Swal.fire({
                title: "Atenção!",
                text: "Por favor, preencha todos os campos obrigatórios.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            
            return false;
        }
        
        // Confirmação final
        e.preventDefault();
        
        Swal.fire({
            title: "Confirmar alterações?",
            text: "Verifique se todos os dados estão corretos antes de confirmar.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sim, atualizar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#0d6efd",
            cancelButtonColor: "#6c757d"
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar o formulário
                document.getElementById("form-agendamento").submit();
            }
        });
    });
    
    // Sugestões de empreiteiras
    const empreiteiras = [
        "Construtora ABC",
        "Terraplanagem XYZ",
        "Montagens Industriais",
        "Transportadora Rápida",
        "Fazenda Esperança",
        "Construtora Silva",
        "Empreiteira Oliveira",
        "Transportes Santos",
        "Logística Expressa",
        "Construções Modernas"
    ];
    
    // Implementação simples de autocomplete
    const empreiteiraInput = document.getElementById("empreiteira");
    
    empreiteiraInput.addEventListener("input", function() {
        const valor = this.value.toLowerCase();
        const sugestoes = empreiteiras.filter(e => e.toLowerCase().includes(valor));
        
        // Remover lista de sugestões existente
        const listaSugestoes = document.getElementById("sugestoes-empreiteira");
        if (listaSugestoes) {
            listaSugestoes.remove();
        }
        
        // Se não houver sugestões ou o campo estiver vazio, não mostrar nada
        if (sugestoes.length === 0 || valor === "") {
            return;
        }
        
        // Criar lista de sugestões
        const lista = document.createElement("div");
        lista.id = "sugestoes-empreiteira";
        lista.className = "list-group position-absolute w-100 shadow-sm";
        lista.style.zIndex = "1000";
        
        sugestoes.forEach(sugestao => {
            const item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action";
            item.textContent = sugestao;
            
            item.addEventListener("click", function() {
                empreiteiraInput.value = sugestao;
                lista.remove();
            });
            
            lista.appendChild(item);
        });
        
        // Adicionar lista após o input
        empreiteiraInput.parentNode.appendChild(lista);
    });
    
    // Fechar lista de sugestões ao clicar fora
    document.addEventListener("click", function(e) {
        if (e.target !== empreiteiraInput) {
            const listaSugestoes = document.getElementById("sugestoes-empreiteira");
            if (listaSugestoes) {
                listaSugestoes.remove();
            }
        }
    });
});
</script>

<style>
.disponibilidade-horarios .btn {
    margin-bottom: 5px;
    font-size: 0.85rem;
}
.btn-horario.active {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.5);
}
</style>';
?>