/**
 * Script principal do Dashboard - Versão Simplificada
 * Foco em usabilidade e clareza para usuários finais
 */
document.addEventListener("DOMContentLoaded", function () {
    // =====================================================================
    // 1. INICIALIZAÇÃO E CONFIGURAÇÃO
    // =====================================================================

    // 1.1 Definição de variáveis e constantes iniciais
    const hoje = new Date();
    const inicioMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
    const fimMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
    const formatoData = { year: 'numeric', month: '2-digit', day: '2-digit' };

    // 1.2 Variáveis globais para controle
    let filtroDataInicio = inicioMes.toISOString().split('T')[0];
    let filtroDataFim = fimMes.toISOString().split('T')[0];
    let calendar; // Referência global para o calendário
    let dataSelecionada = hoje.toISOString().split('T')[0]; // Data selecionada no calendário

    // 1.3 Configuração do seletor de datas (Flatpickr)
    const datePickerOptions = {
        dateFormat: "Y-m-d",
        locale: "pt",
        allowInput: true,
        disableMobile: true,
        altInput: true,
        altFormat: "d/m/Y"
    };

    // 1.4 Inicialização dos seletores de data
    const fpInicio = flatpickr("#filtro-data-inicio", {
        ...datePickerOptions,
        defaultDate: filtroDataInicio,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                filtroDataInicio = selectedDates[0].toISOString().split('T')[0];
                atualizarTextoPeriodoPersonalizado();
            }
        }
    });

    const fpFim = flatpickr("#filtro-data-fim", {
        ...datePickerOptions,
        defaultDate: filtroDataFim,
        onChange: function (selectedDates) {
            if (selectedDates.length > 0) {
                filtroDataFim = selectedDates[0].toISOString().split('T')[0];
                atualizarTextoPeriodoPersonalizado();
            }
        }
    });

    const datePicker = flatpickr("#data_disponibilidade", {
        ...datePickerOptions,
        defaultDate: "today",
        disable: [
            function (date) {
                return (date.getDay() === 0 || date.getDay() === 6); // Desabilita finais de semana
            }
        ],
        onChange: function (selectedDates, dateStr) {
            dataSelecionada = dateStr;
            carregarHorariosDisponiveis(dateStr);
        }
    });

    // 1.5 Configuração inicial da interface
    destacarBotaoPeriodoAtivo("btn-periodo-mes");
    const nomeMes = hoje.toLocaleDateString('pt-BR', { month: 'long' });
    atualizarTextoFiltro(`Filtro: ${nomeMes} de ${hoje.getFullYear()}`);
    atualizarTextoPeriodoPersonalizado();

    // =====================================================================
    // 2. FUNÇÕES AUXILIARES
    // =====================================================================

    /**
     * Obtém o caminho base da aplicação
     * @returns {string} Caminho base da aplicação
     */
    function getBasePath() {
        // Verificar se há uma variável global definida com o caminho base
        if (typeof baseUrl !== 'undefined') {
            return baseUrl;
        }

        // Tentar detectar o caminho base a partir da URL atual
        const pathSegments = window.location.pathname.split('/');
        if (pathSegments.includes('projeto-agendamento')) {
            const baseIndex = pathSegments.indexOf('projeto-agendamento');
            return window.location.origin + '/' + pathSegments.slice(0, baseIndex + 2).join('/');
        }

        // Fallback para o caminho padrão
        return window.location.origin;
    }

    /**
     * Atualiza o texto do período personalizado com base nos valores dos campos de data
     */
    function atualizarTextoPeriodoPersonalizado() {
        const dataInicio = fpInicio.altInput ? fpInicio.altInput.value : document.getElementById("filtro-data-inicio").value;
        const dataFim = fpFim.altInput ? fpFim.altInput.value : document.getElementById("filtro-data-fim").value;

        const periodoTexto = document.getElementById("periodo-personalizado-texto");
        if (periodoTexto) {
            periodoTexto.textContent = `${dataInicio} até ${dataFim}`;
        }
    }

    /**
     * Atualiza o texto do filtro atual
     * @param {string} texto - Texto a ser exibido
     */
    function atualizarTextoFiltro(texto) {
        const filtroAtual = document.getElementById("filtro-atual");
        if (filtroAtual) {
            filtroAtual.textContent = texto;
        }
    }

    /**
     * Destaca visualmente o botão de período ativo
     * @param {string|null} botaoId - ID do botão a ser destacado
     */
    function destacarBotaoPeriodoAtivo(botaoId) {
        document.querySelectorAll('[id^="btn-periodo-"]').forEach(btn => {
            btn.classList.remove('active');
        });

        if (botaoId) {
            const botao = document.getElementById(botaoId);
            if (botao) botao.classList.add('active');
        }
    }

    /**
     * Formata uma data para exibição
     * @param {string} dataStr - Data no formato ISO
     * @returns {string} Data formatada
     */
    function formatarData(dataStr) {
        const data = new Date(dataStr);
        return data.toLocaleDateString('pt-BR');
    }

    /**
     * Atualiza o destaque visual dos dias selecionados no calendário
     * @param {string|Date} dataInicio - Data de início do período
     * @param {string|Date} dataFim - Data de fim do período
     */
    function destacarPeriodoCalendario(dataInicio, dataFim) {
        // Converter para objetos Date se forem strings
        if (typeof dataInicio === 'string') dataInicio = new Date(dataInicio);
        if (typeof dataFim === 'string') dataFim = new Date(dataFim);

        // Normalizar as datas (remover horas)
        dataInicio.setHours(0, 0, 0, 0);
        dataFim.setHours(0, 0, 0, 0);

        // Remover classes de destaque anteriores
        document.querySelectorAll('.fc-day').forEach(el => {
            el.classList.remove('dia-selecionado', 'dia-periodo-inicio', 'dia-periodo-fim', 'dia-periodo');
        });

        // Se as datas são iguais, destacar apenas um dia
        if (dataInicio.getTime() === dataFim.getTime()) {
            const dataStr = dataInicio.toISOString().split('T')[0];
            const celula = document.querySelector(`.fc-day[data-date="${dataStr}"]`);
            if (celula) celula.classList.add('dia-selecionado');
            return;
        }

        // Destacar o período inteiro
        let dataAtual = new Date(dataInicio);
        while (dataAtual <= dataFim) {
            const dataStr = dataAtual.toISOString().split('T')[0];
            const celula = document.querySelector(`.fc-day[data-date="${dataStr}"]`);

            if (celula) {
                // Adicionar classe para o período
                celula.classList.add('dia-periodo');

                // Adicionar classes específicas para início e fim
                if (dataAtual.getTime() === dataInicio.getTime()) {
                    celula.classList.add('dia-periodo-inicio');
                }

                if (dataAtual.getTime() === dataFim.getTime()) {
                    celula.classList.add('dia-periodo-fim');
                }
            }

            // Avançar para o próximo dia
            dataAtual.setDate(dataAtual.getDate() + 1);
        }
    }

    /**
     * Verifica e atualiza a disponibilidade de horários
     * @param {string} data - Data no formato ISO (YYYY-MM-DD)
     * @param {string} hora - Hora no formato HH:MM
     * @param {boolean} disponivel - Se o horário deve ficar disponível ou não
     */
    function atualizarDisponibilidadeHorario(data, hora, disponivel) {
        // Exibir indicador de carregamento
        Swal.fire({
            title: 'Atualizando...',
            text: 'Aguarde enquanto atualizamos a disponibilidade',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fazer requisição para atualizar disponibilidade
        fetch(`${getBasePath()}/dashboard/atualizar-disponibilidade`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                data: data,
                hora: hora,
                disponivel: disponivel
            })
        })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao atualizar disponibilidade');
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Disponibilidade atualizada com sucesso',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Recarregar os horários disponíveis
                carregarHorariosDisponiveis(dataSelecionada);
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Não foi possível atualizar a disponibilidade',
                    icon: 'error'
                });
            });
    }

    // =====================================================================
    // 3. MANIPULADORES DE EVENTOS PARA FILTROS DE DATA
    // =====================================================================

    // Botão "Hoje"
    if (document.getElementById("btn-periodo-hoje")) {
        document.getElementById("btn-periodo-hoje").addEventListener("click", function () {
            const hoje = new Date();
            const dataFormatada = hoje.toLocaleDateString('pt-BR', formatoData);
            const dataISO = hoje.toISOString().split('T')[0];

            fpInicio.setDate(dataISO);
            fpFim.setDate(dataISO);
            filtroDataInicio = dataISO;
            filtroDataFim = dataISO;

            atualizarTextoFiltro(`Filtro: Hoje (${dataFormatada})`);
            atualizarTextoPeriodoPersonalizado();
            destacarBotaoPeriodoAtivo("btn-periodo-hoje");
            destacarPeriodoCalendario(hoje, hoje);

            if (calendar) {
                calendar.gotoDate(dataISO);
            }

            carregarDadosFiltrados();
        });
    }

    // Botão "Esta Semana"
    if (document.getElementById("btn-periodo-semana")) {
        document.getElementById("btn-periodo-semana").addEventListener("click", function () {
            const hoje = new Date();
            const diaSemana = hoje.getDay();
            const inicioSemana = new Date(hoje);
            inicioSemana.setDate(hoje.getDate() - (diaSemana === 0 ? 6 : diaSemana - 1));

            const fimSemana = new Date(inicioSemana);
            fimSemana.setDate(inicioSemana.getDate() + 6);

            const inicioISO = inicioSemana.toISOString().split('T')[0];
            const fimISO = fimSemana.toISOString().split('T')[0];

            fpInicio.setDate(inicioISO);
            fpFim.setDate(fimISO);
            filtroDataInicio = inicioISO;
            filtroDataFim = fimISO;

            atualizarTextoFiltro(`Filtro: Semana atual (${formatarData(inicioISO)} - ${formatarData(fimISO)})`);
            atualizarTextoPeriodoPersonalizado();
            destacarBotaoPeriodoAtivo("btn-periodo-semana");
            destacarPeriodoCalendario(inicioSemana, fimSemana);

            if (calendar) {
                calendar.gotoDate(inicioISO);
            }

            carregarDadosFiltrados();
        });
    }

    // Botão "Este Mês"
    if (document.getElementById("btn-periodo-mes")) {
        document.getElementById("btn-periodo-mes").addEventListener("click", function () {
            const hoje = new Date();
            const inicioMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
            const fimMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);

            const inicioISO = inicioMes.toISOString().split('T')[0];
            const fimISO = fimMes.toISOString().split('T')[0];

            fpInicio.setDate(inicioISO);
            fpFim.setDate(fimISO);
            filtroDataInicio = inicioISO;
            filtroDataFim = fimISO;

            const nomeMes = inicioMes.toLocaleDateString('pt-BR', { month: 'long' });
            atualizarTextoFiltro(`Filtro: ${nomeMes} de ${hoje.getFullYear()}`);
            atualizarTextoPeriodoPersonalizado();
            destacarBotaoPeriodoAtivo("btn-periodo-mes");
            destacarPeriodoCalendario(inicioMes, fimMes);

            if (calendar) {
                calendar.gotoDate(inicioISO);
            }

            carregarDadosFiltrados();
        });
    }

    // Botão "Este Ano"
    if (document.getElementById("btn-periodo-ano")) {
        document.getElementById("btn-periodo-ano").addEventListener("click", function () {
            const hoje = new Date();
            const inicioAno = new Date(hoje.getFullYear(), 0, 1);
            const fimAno = new Date(hoje.getFullYear(), 11, 31);

            const inicioISO = inicioAno.toISOString().split('T')[0];
            const fimISO = fimAno.toISOString().split('T')[0];

            fpInicio.setDate(inicioISO);
            fpFim.setDate(fimISO);
            filtroDataInicio = inicioISO;
            filtroDataFim = fimISO;

            atualizarTextoFiltro(`Filtro: Ano de ${hoje.getFullYear()}`);
            atualizarTextoPeriodoPersonalizado();
            destacarBotaoPeriodoAtivo("btn-periodo-ano");
            destacarPeriodoCalendario(inicioAno, fimAno);

            if (calendar) {
                calendar.gotoDate(inicioISO);
            }

            carregarDadosFiltrados();
        });
    }

    // Botão "Limpar Filtro"
    if (document.getElementById("btn-limpar-filtro")) {
        document.getElementById("btn-limpar-filtro").addEventListener("click", function () {
            const hoje = new Date();
            const inicioMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
            const fimMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);

            const inicioISO = inicioMes.toISOString().split('T')[0];
            const fimISO = fimMes.toISOString().split('T')[0];

            fpInicio.setDate(inicioISO);
            fpFim.setDate(fimISO);
            filtroDataInicio = inicioISO;
            filtroDataFim = fimISO;

            atualizarTextoFiltro("Visão geral dos agendamentos");
            atualizarTextoPeriodoPersonalizado();
            destacarBotaoPeriodoAtivo(null);

            // Remover destaque do calendário
            document.querySelectorAll('.fc-day').forEach(el => {
                el.classList.remove('dia-selecionado', 'dia-periodo-inicio', 'dia-periodo-fim', 'dia-periodo');
            });

            if (calendar) {
                calendar.gotoDate(inicioISO);
            }

            // Fechar o collapse
            if (typeof bootstrap !== 'undefined') {
                const bsCollapse = bootstrap.Collapse.getInstance(document.getElementById('filtroCollapse'));
                if (bsCollapse) bsCollapse.hide();
            }

            carregarDadosFiltrados();

            Swal.fire({
                title: "Filtro limpo!",
                text: "Mostrando dados do mês atual",
                icon: "success",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000
            });
        });
    }

    // Botão "Aplicar Filtro"
    if (document.getElementById("btn-aplicar-filtro")) {
        document.getElementById("btn-aplicar-filtro").addEventListener("click", function () {
            const dataInicioRaw = fpInicio.altInput ? fpInicio.altInput.value : document.getElementById("filtro-data-inicio").value;
            const dataFimRaw = fpFim.altInput ? fpFim.altInput.value : document.getElementById("filtro-data-fim").value;

            atualizarTextoFiltro(`Filtro personalizado: ${dataInicioRaw} - ${dataFimRaw}`);
            destacarBotaoPeriodoAtivo(null);
            destacarPeriodoCalendario(new Date(filtroDataInicio), new Date(filtroDataFim));

            if (calendar) {
                calendar.gotoDate(filtroDataInicio);
            }

            carregarDadosFiltrados();
        });
    }

    // =====================================================================
    // 4. FUNÇÕES DE CARREGAMENTO DE DADOS
    // =====================================================================

    /**
     * Carrega dados filtrados por período via AJAX
     */
    function carregarDadosFiltrados() {
        fetch(`${getBasePath()}/dashboard/dados-filtrados?data_inicio=${filtroDataInicio}&data_fim=${filtroDataFim}`)
            .then(response => {
                if (!response.ok) throw new Error("Erro na requisição: " + response.status);
                return response.json();
            })
            .then(data => {
                // Atualiza os indicadores
                document.getElementById("total-agendamentos").textContent = data.total_agendamentos || 0;
                document.getElementById("taxa-confirmacao").textContent = (data.taxa_confirmacao?.taxa || 0) + "%";
                document.getElementById("agendamentos-hoje").textContent = data.agendamentos_hoje || 0;
                document.getElementById("agendamentos-semana").textContent = data.agendamentos_semana || 0;

                // Atualiza os gráficos
                atualizarGraficoTipos(data.grafico_tipos);
                atualizarGraficoDiasSemana(data.grafico_dias_semana);
                atualizarGraficoHoras(data.grafico_horas);
                atualizarGraficoMeses(data.grafico_meses);

                // Atualiza o calendário
                if (calendar) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(data.eventos_calendario);
                }

                Swal.fire({
                    title: "Dados atualizados!",
                    text: `Mostrando dados de ${formatarData(filtroDataInicio)} até ${formatarData(filtroDataFim)}`,
                    icon: "success",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000
                });
            })
            .catch(error => {
                console.error("Erro ao carregar dados filtrados:", error);
                Swal.fire({
                    title: "Erro!",
                    text: "Não foi possível carregar os dados filtrados.",
                    icon: "error"
                });
            });
    }

    function carregarHorariosDisponiveis(data) {
        const container = document.getElementById("horarios-disponiveis-dashboard");
        if (!container) return;

        container.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando horários disponíveis...</p></div>';

        // Usar a variável global baseUrl
        fetch(`${getBasePath()}/dashboard/horarios-disponiveis?data=${data}`)
            .then(response => {
                if (!response.ok) throw new Error("Erro na requisição: " + response.status);
                return response.json();
            })
            .then(data => {
                renderizarHorariosDisponiveis(data.horarios || [], container);
            })
            .catch(error => {
                console.error("Erro ao consultar horários:", error);
                container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao consultar horários disponíveis</div>';
            });
    }

    /**
     * Renderiza os horários disponíveis de forma simplificada
     * @param {Array} horarios - Lista de horários
     * @param {HTMLElement} container - Container onde será renderizado
     */
    function renderizarHorariosDisponiveis(horarios, container) {
        if (!horarios || horarios.length === 0) {
            container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i> Nenhum horário disponível para esta data.</div>';
            return;
        }

        // Verificar se o usuário é administrador
        const isAdmin = document.querySelector('meta[name="user-role"]')?.getAttribute('content') === 'admin';

        // Separar horários por período (manhã/tarde)
        const manha = horarios.filter(h => parseInt(h.horario.split(':')[0]) < 12);
        const tarde = horarios.filter(h => parseInt(h.horario.split(':')[0]) >= 12);

        // Formatar a data selecionada
        const dataObj = new Date(dataSelecionada);
        const dataFormatada = dataObj.toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        let html = `
        <div class="horarios-container p-3 border rounded bg-light">
            <h6 class="text-center mb-3">
                <i class="fas fa-calendar-day me-2"></i>${dataFormatada}
            </h6>
            
            <!-- Manhã -->
            <div class="mb-3">
                <h6 class="periodo-titulo">
                    <i class="fas fa-sun me-2"></i>Manhã
                </h6>
                <div class="d-flex flex-wrap gap-2">
    `;

        // Adicionar horários da manhã
        manha.forEach(horario => {
            const disponivel = horario.disponivel;
            const btnClass = disponivel ? "btn-outline-success" : "btn-outline-secondary";
            const disabled = isAdmin ? "" : (disponivel ? "" : "disabled");
            const icon = disponivel ? "check" : "times";

            html += `
            <button type="button" class="btn ${btnClass} btn-sm ${disabled}" 
                data-hora="${horario.horario}" 
                data-disponivel="${disponivel ? 1 : 0}">
                <i class="fas fa-${icon} me-1"></i>${horario.horario}
                ${isAdmin ? '<span class="ms-1 badge bg-light text-dark">⚙️</span>' : ''}
            </button>
        `;
        });

        html += `
                </div>
            </div>
            
            <!-- Tarde -->
            <div class="mb-3">
                <h6 class="periodo-titulo">
                    <i class="fas fa-moon me-2"></i>Tarde
                </h6>
                <div class="d-flex flex-wrap gap-2">
    `;

        // Adicionar horários da tarde
        tarde.forEach(horario => {
            const disponivel = horario.disponivel;
            const btnClass = disponivel ? "btn-outline-success" : "btn-outline-secondary";
            const disabled = isAdmin ? "" : (disponivel ? "" : "disabled");
            const icon = disponivel ? "check" : "times";

            html += `
            <button type="button" class="btn ${btnClass} btn-sm ${disabled}" 
                data-hora="${horario.horario}" 
                data-disponivel="${disponivel ? 1 : 0}">
                <i class="fas fa-${icon} me-1"></i>${horario.horario}
                ${isAdmin ? '<span class="ms-1 badge bg-light text-dark">⚙️</span>' : ''}
            </button>
        `;
        });

        // Resumo de disponibilidade
        const totalDisponiveis = horarios.filter(h => h.disponivel).length;
        const totalHorarios = horarios.length;
        const percentualDisponivel = Math.round((totalDisponiveis / totalHorarios) * 100);

        html += `
                </div>
            </div>
            
            <!-- Resumo -->
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Disponibilidade</span>
                    <span>${totalDisponiveis} de ${totalHorarios} horários</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: ${percentualDisponivel}%;" 
                        aria-valuenow="${percentualDisponivel}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mt-1">
                    <small class="text-muted">${percentualDisponivel}% disponível</small>
                </div>
            </div>
    `;

        // Botão para criar agendamento
        if (document.querySelector('.nav-item a[href*="agendamentos/criar"]')) {
            html += `
            <div class="mt-3 text-center">
                <a href="${getBasePath()}/agendamentos/criar?data=${dataSelecionada}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Criar Agendamento nesta Data
                </a>
            </div>
        `;
        }

        html += '</div>'; // Fim do container

        container.innerHTML = html;

        // Adicionar event listeners para os botões de horário
        container.querySelectorAll('.btn[data-hora]').forEach(btn => {
            btn.addEventListener('click', function () {
                const hora = this.getAttribute('data-hora');
                const disponivel = this.getAttribute('data-disponivel') === '1';

                if (isAdmin) {
                    // Para administradores, mostrar opções
                    Swal.fire({
                        title: `Horário: ${hora}`,
                        html: `
                        <p>O que você deseja fazer com este horário?</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button id="btn-agendar" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-1"></i>Agendar
                            </button>
                            <button id="btn-toggle" class="btn btn-${disponivel ? 'warning' : 'success'}">
                                <i class="fas fa-${disponivel ? 'lock' : 'unlock'} me-1"></i>
                                ${disponivel ? 'Bloquear' : 'Disponibilizar'}
                            </button>
                        </div>
                    `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Fechar',
                        didOpen: () => {
                            // Botão de agendar
                            document.getElementById('btn-agendar').addEventListener('click', () => {
                                window.location.href = `${getBasePath()}/agendamentos/criar?data=${dataSelecionada}&hora=${hora}`;
                                Swal.close();
                            });

                            // Botão de alternar disponibilidade
                            document.getElementById('btn-toggle').addEventListener('click', () => {
                                atualizarDisponibilidadeHorario(dataSelecionada, hora, !disponivel);
                                Swal.close();
                            });
                        }
                    });
                } else if (disponivel) {
                    // Para usuários normais, apenas redirecionar se disponível
                    window.location.href = `${getBasePath()}/agendamentos/criar?data=${dataSelecionada}&hora=${hora}`;
                }
            });
        });
    }

    // =====================================================================
    // 5. INICIALIZAÇÃO DO CALENDÁRIO
    // =====================================================================

    /**
     * Inicializa o calendário FullCalendar com configuração simplificada
     */
    const calendarEl = document.getElementById("calendario-agendamentos");
    if (calendarEl) {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            locale: "pt-br",
            headerToolbar: {
                left: "",
                center: "title",
                right: ""
            },
            height: "auto", // Altura automática para evitar expansão excessiva
            dayMaxEvents: 3, // Limitar número de eventos visíveis por dia
            events: (typeof dadosDashboard !== 'undefined' && dadosDashboard.eventos_calendario) ? dadosDashboard.eventos_calendario : [],
            selectable: false, // Desabilitar seleção múltipla
            dateClick: function (info) {
                // Ao clicar em uma data, atualizar a data selecionada
                dataSelecionada = info.dateStr;

                // Atualizar o campo de data de disponibilidade
                datePicker.setDate(dataSelecionada);

                // Carregar horários disponíveis
                carregarHorariosDisponiveis(dataSelecionada);

                // Destacar visualmente a data selecionada
                document.querySelectorAll('.fc-day').forEach(el => {
                    el.classList.remove('dia-selecionado');
                });
                info.dayEl.classList.add('dia-selecionado');

                // Rolar suavemente para a seção de horários disponíveis
                document.getElementById("horarios-disponiveis-dashboard").scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            },
            eventClick: function (info) {
                // Ao clicar em um evento, mostrar detalhes
                const evento = info.event;
                const dataHora = new Date(evento.start);

                Swal.fire({
                    title: evento.title,
                    html: `
                        <div class="text-start">
                            <p><strong>Data/Hora:</strong> ${dataHora.toLocaleString("pt-BR")}</p>
                            <p><strong>Empreiteira:</strong> ${evento.extendedProps.empreiteira || "N/A"}</p>
                            <p><strong>Tipo:</strong> ${evento.extendedProps.tipo || "N/A"}</p>
                            <p><strong>Confirmado:</strong> ${evento.extendedProps.confirmado ? "Sim" : "Não"}</p>
                        </div>
                    `,
                    icon: "info",
                    confirmButtonText: "Fechar",
                    showCancelButton: true,
                    cancelButtonText: "Editar",
                    cancelButtonColor: "#3085d6"
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = `${getBasePath()}/agendamentos/editar/${evento.id}`;
                    }
                });
            },
            eventDidMount: function (info) {
                // Adicionar tooltip aos eventos para melhor visualização
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(info.el, {
                        title: `${info.event.title} - ${info.event.extendedProps.tipo || ""}`,
                        placement: "top",
                        trigger: "hover",
                        container: "body"
                    });
                }
            },
            dayCellDidMount: function (info) {
                // Adicionar classes para estilização
                const dataAtual = info.date;
                const hoje = new Date();

                // Normalizar datas para comparação
                dataAtual.setHours(0, 0, 0, 0);
                hoje.setHours(0, 0, 0, 0);

                // Verificar se é hoje
                if (dataAtual.getTime() === hoje.getTime()) {
                    info.el.classList.add('dia-hoje');
                }

                // Verificar se tem eventos neste dia
                const eventos = calendar.getEvents().filter(evento => {
                    const dataEvento = new Date(evento.start);
                    dataEvento.setHours(0, 0, 0, 0);
                    return dataEvento.getTime() === dataAtual.getTime();
                });

                if (eventos.length > 0) {
                    info.el.classList.add('dia-com-eventos');

                    // Adicionar badge com quantidade de eventos
                    if (eventos.length > 3) {
                        const badge = document.createElement('span');
                        badge.className = 'evento-badge';
                        badge.textContent = eventos.length;
                        info.el.appendChild(badge);
                    }
                }
            }
        });

        calendar.render();

        // Botões de navegação do calendário
        const btnMesAnterior = document.getElementById("btn-mes-anterior");
        const btnMesAtual = document.getElementById("btn-mes-atual");
        const btnMesProximo = document.getElementById("btn-mes-proximo");

        if (btnMesAnterior) {
            btnMesAnterior.addEventListener("click", function () {
                calendar.prev();
            });
        }

        if (btnMesAtual) {
            btnMesAtual.addEventListener("click", function () {
                calendar.today();
            });
        }

        if (btnMesProximo) {
            btnMesProximo.addEventListener("click", function () {
                calendar.next();
            });
        }
    }

    // =====================================================================
    // 6. FUNÇÕES DE ATUALIZAÇÃO DE GRÁFICOS
    // =====================================================================

    /**
     * Atualiza o gráfico de tipos de agendamento
     * @param {Object} dados - Dados para o gráfico
     */
    function atualizarGraficoTipos(dados) {
        const ctxTipos = document.getElementById("grafico-tipos");
        if (!ctxTipos) return;

        // Destruir gráfico existente se houver
        if (window.graficoTipos) {
            window.graficoTipos.destroy();
        }

        window.graficoTipos = new Chart(ctxTipos, {
            type: "doughnut",
            data: {
                labels: dados.labels || [],
                datasets: [{
                    data: dados.values || [],
                    backgroundColor: dados.backgroundColors || [],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || "";
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Atualiza o gráfico de agendamentos por dia da semana
     * @param {Object} dados - Dados para o gráfico
     */
    function atualizarGraficoDiasSemana(dados) {
        const ctxDiasSemana = document.getElementById("grafico-dias-semana");
        if (!ctxDiasSemana) return;

        // =====================================================================
        // 7. INICIALIZAÇÃO E CARREGAMENTO INICIAL
        // =====================================================================

        // Verificar se a variável dadosDashboard está definida
        if (typeof dadosDashboard !== 'undefined') {
            // Inicializar gráficos com os dados iniciais
            atualizarGraficoTipos(dadosDashboard.grafico_tipos || { labels: [], values: [], backgroundColors: [] });
            atualizarGraficoDiasSemana(dadosDashboard.grafico_dias_semana || { labels: [], values: [] });
            atualizarGraficoHoras(dadosDashboard.grafico_horas || { labels: [], values: [] });
            atualizarGraficoMeses(dadosDashboard.grafico_meses || { labels: [], values: [] });

            // Carregar horários disponíveis para a data atual
            carregarHorariosDisponiveis(dataSelecionada);

            // Destacar o período inicial (mês atual)
            destacarPeriodoCalendario(inicioMes, fimMes);

        } else {
            console.warn("Dados do dashboard não estão disponíveis");
        }
        // Destruir gráfico existente se houver
        if (window.graficoDiasSemana) {
            window.graficoDiasSemana.destroy();
        }

        window.graficoDiasSemana = new Chart(ctxDiasSemana, {
            type: "bar",
            data: {
                labels: dados.labels || [],
                datasets: [{
                    label: "Agendamentos",
                    data: dados.values || [],
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + " agendamento(s)";
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    /**
     * Atualiza o gráfico de agendamentos por hora
     * @param {Object} dados - Dados para o gráfico
     */
    function atualizarGraficoHoras(dados) {
        const ctxHoras = document.getElementById("grafico-horas");
        if (!ctxHoras) return;

        // Destruir gráfico existente se houver
        if (window.graficoHoras) {
            window.graficoHoras.destroy();
        }

        window.graficoHoras = new Chart(ctxHoras, {
            type: "line",
            data: {
                labels: dados.labels || [],
                datasets: [{
                    label: "Agendamentos",
                    data: dados.values || [],
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + " agendamento(s)";
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    /**
     * Atualiza o gráfico de agendamentos por mês
     * @param {Object} dados - Dados para o gráfico
     */
    function atualizarGraficoMeses(dados) {
        const ctxMeses = document.getElementById("grafico-meses");
        if (!ctxMeses) return;

        // Destruir gráfico existente se houver
        if (window.graficoMeses) {
            window.graficoMeses.destroy();
        }

        window.graficoMeses = new Chart(ctxMeses, {
            type: "bar",
            data: {
                labels: dados.labels || [],
                datasets: [{
                    label: "Agendamentos",
                    data: dados.values || [],
                    backgroundColor: "rgba(153, 102, 255, 0.7)",
                    borderColor: "rgba(153, 102, 255, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
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
    }

    // =====================================================================
    // 7. INICIALIZAÇÃO E CARREGAMENTO INICIAL
    // =====================================================================

    // Verificar se a variável dadosDashboard está definida
    if (typeof dadosDashboard !== 'undefined') {
        // Inicializar gráficos com os dados iniciais
        atualizarGraficoTipos(dadosDashboard.grafico_tipos || { labels: [], values: [], backgroundColors: [] });
        atualizarGraficoDiasSemana(dadosDashboard.grafico_dias_semana || { labels: [], values: [] });
        atualizarGraficoHoras(dadosDashboard.grafico_horas || { labels: [], values: [] });
        atualizarGraficoMeses(dadosDashboard.grafico_meses || { labels: [], values: [] });

        // Carregar horários disponíveis para a data atual
        carregarHorariosDisponiveis(dataSelecionada);

        // Destacar o período inicial (mês atual)
        destacarPeriodoCalendario(inicioMes, fimMes);
    } else {
        console.warn("Dados do dashboard não estão disponíveis");
    }
});