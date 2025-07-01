<?php $titulo = 'Novo Agendamento - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-calendar-plus me-2"></i> Novo Agendamento
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
                <form action="/projeto-agendamento/public/agendamentos/salvar" method="post" id="form-agendamento">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="veiculo" class="form-label">Veículo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-truck"></i></span>
                                <input type="text" class="form-control" id="veiculo" name="veiculo" placeholder="Tipo/modelo do veículo" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="empreiteira" class="form-label">Empreiteira <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="empreiteira" name="empreiteira" placeholder="Nome da empreiteira" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Agendamento <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="AGENDADO" selected>Agendado</option>
                                    <option value="EMERGENCIAL">Emergencial</option>
                                    <option value="REAGENDADO">Reagendado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="identificador" class="form-label">Identificador <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="identificador" name="identificador" placeholder="Placa ou código do veículo" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="data_agendamento" class="form-label">Data e Hora Inicial <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="text" class="form-control" id="data_agendamento" name="data_agendamento"
                                    value="<?= isset($_GET['data']) && isset($_GET['hora']) ? $_GET['data'] . ' ' . $_GET['hora'] : '' ?>"
                                    placeholder="Selecione a data e hora inicial" required readonly>
                            </div>
                            <small class="text-muted">Selecione um horário disponível no painel ao lado</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="duracao" class="form-label">Duração <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <select class="form-select" id="duracao" name="duracao" required>
                                    <option value="30" <?= ($agendamento['duracao'] ?? 30) == 30 ? 'selected' : '' ?>>30 minutos</option>
                                    <option value="60" <?= ($agendamento['duracao'] ?? 30) == 60 ? 'selected' : '' ?>>1 hora</option>
                                    <option value="90" <?= ($agendamento['duracao'] ?? 30) == 90 ? 'selected' : '' ?>>1 hora e 30 minutos</option>
                                    <option value="120" <?= ($agendamento['duracao'] ?? 30) == 120 ? 'selected' : '' ?>>2 horas</option>
                                    <option value="150" <?= ($agendamento['duracao'] ?? 30) == 150 ? 'selected' : '' ?>>2 horas e 30 minutos</option>
                                    <option value="180" <?= ($agendamento['duracao'] ?? 30) == 180 ? 'selected' : '' ?>>3 horas</option>
                                    <option value="210" <?= ($agendamento['duracao'] ?? 30) == 210 ? 'selected' : '' ?>>3 horas e 30 minutos</option>
                                    <option value="240" <?= ($agendamento['duracao'] ?? 30) == 240 ? 'selected' : '' ?>>4 horas</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="documento" class="form-label">Documento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control" id="documento" name="documento" placeholder="Número do documento (opcional)">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="confirmacao" name="confirmacao" value="1">
                                <label class="form-check-label" for="confirmacao">Agendamento confirmado</label>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo dos horários selecionados -->
                    <div class="mt-3 mb-3">
                        <label class="form-label">Horários Selecionados</label>
                        <div id="horarios-selecionados" class="p-3 border rounded bg-light">
                            <p class="text-muted mb-0">Nenhum horário selecionado. Selecione um horário inicial e uma duração.</p>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/projeto-agendamento/public/agendamentos" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Salvar Agendamento
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
                        <input type="text" id="data_consulta" class="form-control datepicker"
                            placeholder="Selecione uma data"
                            value="<?= isset($_GET['data']) ? $_GET['data'] : date('Y-m-d') ?>">
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
                    <i class="fas fa-info-circle me-2"></i> Selecione um horário inicial e a duração desejada.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$scripts = '
<script>
// Função para obter o caminho base
function getBasePath() {
    return "/projeto-agendamento/public";
}

document.addEventListener("DOMContentLoaded", function() {
    // Inicializa Flatpickr para consulta de horários
    const datePicker = flatpickr("#data_consulta", {
        dateFormat: "Y-m-d",
        locale: "pt",
        allowInput: true,
        defaultDate: "' . (isset($_GET['data']) ? $_GET['data'] : 'today') . '",
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
    
    // Carrega horários disponíveis para a data atual ou selecionada
    carregarHorariosDisponiveis(document.getElementById("data_consulta").value);
    
    // Evento para atualizar os horários selecionados quando a duração muda
    document.getElementById("duracao").addEventListener("change", function() {
        atualizarHorariosSelecionados();
    });
    
    // Função para carregar horários disponíveis
    function carregarHorariosDisponiveis(data) {
        // Exibir loading
        document.getElementById("horarios-disponiveis").innerHTML = \'<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2">Carregando horários disponíveis...</p></div>\';
        
        // Fazer requisição AJAX com caminho base correto
        fetch(getBasePath() + "/dashboard/horarios-disponiveis?data=" + data)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Erro na requisição: " + response.status);
                }
                return response.json();
            })
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
                        const classe = disponivel ? "btn-outline-success" : "btn-outline-secondary";
                        const icon = disponivel ? "check" : "times";
                        const disabled = disponivel ? "" : "disabled";
                        
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
                        const classe = disponivel ? "btn-outline-success" : "btn-outline-secondary";
                        const icon = disponivel ? "check" : "times";
                        const disabled = disponivel ? "" : "disabled";
                        
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
                            if (b.classList.contains("btn-success")) {
                                b.classList.replace("btn-success", "btn-outline-success");
                            }
                        });
                        this.classList.add("active");
                        this.classList.replace("btn-outline-success", "btn-success");
                        
                        // Atualizar os horários selecionados
                        atualizarHorariosSelecionados();
                        
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
                
                // Se já tiver data e hora na URL, selecionar automaticamente
                if ("' . (isset($_GET['data']) && isset($_GET['hora']) ? 'true' : 'false') . '" === "true") {
                    const dataUrl = "' . (isset($_GET['data']) ? $_GET['data'] : '') . '";
                    const horaUrl = "' . (isset($_GET['hora']) ? $_GET['hora'] : '') . '";
                    
                    document.querySelectorAll(".btn-horario").forEach(btn => {
                        const horario = btn.getAttribute("data-horario");
                        const data = btn.getAttribute("data-data");
                        
                        if (data === dataUrl && horario === horaUrl) {
                            btn.click();
                        }
                    });
                }
            })
            .catch(error => {
                console.error("Erro ao consultar horários:", error);
                document.getElementById("horarios-disponiveis").innerHTML = \'<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao consultar horários disponíveis. Tente novamente.</div>\';
            });
    }
    
    // Função para atualizar os horários selecionados com base na duração
    function atualizarHorariosSelecionados() {
        const dataAgendamento = document.getElementById("data_agendamento").value;
        const duracao = parseInt(document.getElementById("duracao").value);
        const horariosContainer = document.getElementById("horarios-selecionados");
        
        if (!dataAgendamento) {
            horariosContainer.innerHTML = \'<p class="text-muted mb-0">Nenhum horário selecionado. Selecione um horário inicial e uma duração.</p>\';
            return;
        }
        
        // Extrair data e hora inicial
        const [data, horaInicial] = dataAgendamento.split(" ");
        
        // Calcular os horários subsequentes com base na duração
        const horariosSelecionados = calcularHorariosConsecutivos(data, horaInicial, duracao);
        
        // Verificar se todos os horários estão disponíveis
        const todosDisponiveis = verificarDisponibilidadeHorarios(horariosSelecionados);
        
        // Exibir os horários selecionados
        let html = \'\';
        
        if (todosDisponiveis) {
            html += \'<div class="alert alert-success mb-2">\';
            html += \'<i class="fas fa-check-circle me-2"></i> Todos os horários estão disponíveis\';
            html += \'</div>\';
        } else {
            html += \'<div class="alert alert-danger mb-2">\';
            html += \'<i class="fas fa-exclamation-triangle me-2"></i> Alguns horários não estão disponíveis. Ajuste a duração ou selecione outro horário inicial.\';
            html += \'</div>\';
        }
        
        html += \'<div class="d-flex flex-wrap gap-2">\';
        
        horariosSelecionados.forEach((horario, index) => {
            const disponivel = horario.disponivel;
            const classe = disponivel ? "bg-success" : "bg-danger";
            const textoHora = horario.hora;
            
            html += \'<div class="badge \' + classe + \' text-white p-2">\';
            html += \'<i class="fas fa-\' + (disponivel ? \'check\' : \'times\') + \' me-1"></i>\';
            html += textoHora;
            html += \'</div>\';
        });
        
        html += \'</div>\';
        
        // Adicionar horário final
        if (horariosSelecionados.length > 0) {
            const horaInicio = horaInicial;
            const ultimoHorario = horariosSelecionados[horariosSelecionados.length - 1].hora;
            
            html += \'<div class="mt-2 text-muted">\';
            html += \'<small>Horário de início: <strong>\' + horaInicio + \'</strong> | \';
            html += \'Horário de término: <strong>\' + ultimoHorario + \'</strong> | \';
            html += \'Duração total: <strong>\' + (duracao / 60).toFixed(1).replace(\'.\', \',\') + \' hora(s)</strong></small>\';
            html += \'</div>\';
        }
        
        horariosContainer.innerHTML = html;
        
        // Se algum horário não estiver disponível, desabilitar o botão de salvar
        const btnSalvar = document.querySelector(\'button[type="submit"]\');
        if (!todosDisponiveis) {
            btnSalvar.disabled = true;
            btnSalvar.classList.add("btn-secondary");
            btnSalvar.classList.remove("btn-success");
        } else {
            btnSalvar.disabled = false;
            btnSalvar.classList.add("btn-success");
            btnSalvar.classList.remove("btn-secondary");
        }
    }
    
    // Função para calcular os horários consecutivos com base na duração
    function calcularHorariosConsecutivos(data, horaInicial, duracaoMinutos) {
        const horarios = [];
        const [horaIni, minutoIni] = horaInicial.split(":").map(Number);
        
        // Calcular quantos slots de 30 minutos são necessários
        const numSlots = Math.ceil(duracaoMinutos / 30);
        
        // Adicionar o horário inicial
        horarios.push({
            hora: horaInicial,
            disponivel: verificarDisponibilidadeBotao(data, horaInicial)
        });
        
        // Calcular os horários subsequentes
        let horaAtual = horaIni;
        let minutoAtual = minutoIni;
        
        for (let i = 1; i < numSlots; i++) {
            minutoAtual += 30;
            
            if (minutoAtual >= 60) {
                horaAtual += 1;
                minutoAtual -= 60;
            }
            
            // Formatar a hora
            const horaFormatada = `${horaAtual.toString().padStart(2, "0")}:${minutoAtual.toString().padStart(2, "0")}`;
            
            // Verificar se o horário está disponível
            const disponivel = verificarDisponibilidadeBotao(data, horaFormatada);
            
            horarios.push({
                hora: horaFormatada,
                disponivel: disponivel
            });
        }
        
        return horarios;
    }
    
    // Função para verificar se todos os horários estão disponíveis
    function verificarDisponibilidadeHorarios(horarios) {
        return horarios.every(horario => horario.disponivel);
    }
    
    // Função para verificar se um horário específico está disponível (pelo botão)
    function verificarDisponibilidadeBotao(data, hora) {
        const botao = document.querySelector(`.btn-horario[data-data="${data}"][data-horario="${hora}"]`);
        
        if (botao) {
            // Se o botão existe, verificar se está habilitado
            return !botao.disabled;
        }
        
        // Se o botão não existe, considerar como indisponível
        return false;
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
        const duracao = document.getElementById("duracao").value;
        
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
        
        // Verificar se o usuário realmente selecionou um horário
        if (document.querySelector(".btn-horario.active") === null && !dataAgendamento) {
            e.preventDefault();
            
            Swal.fire({
                title: "Horário não selecionado!",
                text: "Por favor, selecione um horário disponível no painel ao lado.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            
            return false;
        }
        
        // Verificar se todos os horários estão disponíveis
        const [data, horaInicial] = dataAgendamento.split(" ");
        const horariosSelecionados = calcularHorariosConsecutivos(data, horaInicial, parseInt(duracao));
        const todosDisponiveis = verificarDisponibilidadeHorarios(horariosSelecionados);
        
        if (!todosDisponiveis) {
            e.preventDefault();
            
            Swal.fire({
                title: "Horários indisponíveis!",
                text: "Alguns dos horários selecionados não estão disponíveis. Ajuste a duração ou selecione outro horário inicial.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            
            return false;
        }
        
        // Confirmação final
        e.preventDefault();
        
        Swal.fire({
            title: "Confirmar agendamento?",
            text: "Verifique se todos os dados estão corretos antes de confirmar.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sim, agendar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#28a745",
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
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
}
</style>';
?>