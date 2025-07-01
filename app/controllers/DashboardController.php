<?php

// Incluir o modelo Horarios manualmente
require_once __DIR__ . '/../models/Horarios.php';

class DashboardController
{
    private $estatisticasModel;
    private $agendamentoModel;
    private $horariosModel;

    public function __construct()
    {
        $this->estatisticasModel = new Estatisticas();
        $this->agendamentoModel = new Agendamento();
        $this->horariosModel = new Horarios();
    }

    /**
     * Exibe a página de dashboard com indicadores e gráficos
     */
    public function index()
    {
        // Dados para o dashboard
        $total_agendamentos = $this->estatisticasModel->totalAgendamentos();
        $agendamentos_por_tipo = $this->estatisticasModel->agendamentosPorTipo();
        $agendamentos_por_empreiteira = $this->estatisticasModel->agendamentosPorEmpreiteira();
        $agendamentos_por_dia = $this->estatisticasModel->agendamentosPorDiaDaSemana();
        $agendamentos_por_hora = $this->estatisticasModel->agendamentosPorHora();
        $agendamentos_por_mes = $this->estatisticasModel->agendamentosPorMes();
        $taxa_confirmacao = $this->estatisticasModel->taxaConfirmacao();
        $ultimos_agendamentos = $this->estatisticasModel->ultimosAgendamentos(10); // Aumentado para 10
        $agendamentos_hoje = $this->estatisticasModel->agendamentosHoje();
        $agendamentos_semana = $this->estatisticasModel->agendamentosProximaSemana();

        // Preparar dados para o calendário
        $eventos_calendario = $this->prepararDadosCalendario();

        // Preparar dados para gráficos
        $grafico_tipos = $this->prepararDadosGraficoTipos($agendamentos_por_tipo);
        $grafico_empreiteiras = $this->prepararDadosGraficoEmpreiteiras($agendamentos_por_empreiteira);
        $grafico_dias_semana = $this->prepararDadosGraficoDiasSemana($agendamentos_por_dia);
        $grafico_horas = $this->prepararDadosGraficoHoras($agendamentos_por_hora);
        $grafico_meses = $this->prepararDadosGraficoMeses($agendamentos_por_mes);

        // Debug para verificar os dados
        error_log("Dados do gráfico de dias da semana: " . json_encode($grafico_dias_semana));

        // Definir título da página
        $titulo = 'Dashboard - Sistema de Agendamento';

        ob_start();
        require_once __DIR__ . '/../views/dashboard/index.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    /**
     * Retorna os horários disponíveis para uma data específica em formato JSON
     * Esta função é chamada via AJAX pelo dashboard
     */
    public function horariosDisponiveis()
    {
        // Verificar se a data foi informada, caso contrário usar a data atual
        $data = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
        $excluirId = isset($_GET['excluir_id']) ? $_GET['excluir_id'] : null;

        // Obter horários disponíveis do modelo
        $horarios = $this->horariosModel->listarHorariosPorData($data, $excluirId);

        // Retornar como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'data' => $data,
            'horarios' => $horarios
        ]);
        exit;
    }

    /**
     * Atualiza a disponibilidade de um horário
     * Esta função é chamada via AJAX pelo dashboard
     */
    public function atualizarDisponibilidade()
    {
        // Verificar se é uma requisição AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso não permitido']);
            return;
        }

        // Verificar se o usuário é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Permissão negada']);
            return;
        }

        // Obter dados da requisição
        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);

        if (!isset($dados['data']) || !isset($dados['hora']) || !isset($dados['disponivel'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            return;
        }

        // Validar data e hora
        $data = $dados['data'];
        $hora = $dados['hora'];
        $disponivel = (bool) $dados['disponivel'];

        // Atualizar no banco de dados usando o modelo
        $resultado = $this->horariosModel->atualizarDisponibilidadeHorario($data, $hora, $disponivel);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Disponibilidade atualizada com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar disponibilidade']);
        }
    }

    /**
     * Retorna dados filtrados por período para o dashboard em formato JSON
     * Esta função é chamada via AJAX pelo dashboard
     */
    public function dadosFiltrados()
    {
        // Obter parâmetros de filtro
        $dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-01');
        $dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-t');

        // Converter datas do formato brasileiro para o formato MySQL se necessário
        if (strpos($dataInicio, '/') !== false) {
            $partes = explode('/', $dataInicio);
            if (count($partes) === 3) {
                $dataInicio = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            }
        }

        if (strpos($dataFim, '/') !== false) {
            $partes = explode('/', $dataFim);
            if (count($partes) === 3) {
                $dataFim = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            }
        }

        // Obter dados filtrados
        $total_agendamentos = $this->estatisticasModel->totalAgendamentosPeriodo($dataInicio, $dataFim);
        $agendamentos_por_tipo = $this->estatisticasModel->agendamentosPorTipoPeriodo($dataInicio, $dataFim);
        $agendamentos_por_dia = $this->estatisticasModel->agendamentosPorDiaDaSemanaPeriodo($dataInicio, $dataFim);
        $agendamentos_por_hora = $this->estatisticasModel->agendamentosPorHoraPeriodo($dataInicio, $dataFim);
        $agendamentos_por_mes = $this->estatisticasModel->agendamentosPorMesPeriodo($dataInicio, $dataFim);
        $taxa_confirmacao = $this->estatisticasModel->taxaConfirmacaoPeriodo($dataInicio, $dataFim);
        $agendamentos_hoje = $this->estatisticasModel->agendamentosHoje();
        $agendamentos_semana = $this->estatisticasModel->agendamentosProximaSemana();

        // Preparar dados para gráficos
        $grafico_tipos = $this->prepararDadosGraficoTipos($agendamentos_por_tipo);
        $grafico_dias_semana = $this->prepararDadosGraficoDiasSemana($agendamentos_por_dia);
        $grafico_horas = $this->prepararDadosGraficoHoras($agendamentos_por_hora);
        $grafico_meses = $this->prepararDadosGraficoMeses($agendamentos_por_mes);

        // Preparar dados para o calendário
        $eventos_calendario = $this->prepararDadosCalendarioPeriodo($dataInicio, $dataFim);

        // Retornar como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'total_agendamentos' => $total_agendamentos,
            'taxa_confirmacao' => $taxa_confirmacao,
            'agendamentos_hoje' => $agendamentos_hoje,
            'agendamentos_semana' => $agendamentos_semana,
            'grafico_tipos' => $grafico_tipos,
            'grafico_dias_semana' => $grafico_dias_semana,
            'grafico_horas' => $grafico_horas,
            'grafico_meses' => $grafico_meses,
            'eventos_calendario' => $eventos_calendario
        ]);
        exit;
    }

    private function prepararDadosCalendario()
    {
        $agendamentos = $this->agendamentoModel->listarTodos();
        $eventos = [];

        $tipoColors = [
            'AGENDADO' => '#28a745',
            'EMERGENCIAL' => '#dc3545',
            'REAGENDADO' => '#ffc107'
        ];

        foreach ($agendamentos as $agendamento) {
            $eventos[] = [
                'id' => $agendamento['id'],
                'title' => $agendamento['veiculo'],
                'start' => $agendamento['data_agendamento'],
                'backgroundColor' => $tipoColors[$agendamento['tipo']] ?? '#6c757d',
                'borderColor' => $tipoColors[$agendamento['tipo']] ?? '#6c757d',
                'extendedProps' => [
                    'empreiteira' => $agendamento['empreiteira'],
                    'tipo' => $agendamento['tipo'],
                    'identificador' => $agendamento['identificador'],
                    'confirmado' => $agendamento['confirmacao'] ? true : false
                ]
            ];
        }

        return $eventos;
    }

    private function prepararDadosCalendarioPeriodo($dataInicio, $dataFim)
    {
        $agendamentos = $this->agendamentoModel->listarPorPeriodo($dataInicio, $dataFim);
        $eventos = [];

        $tipoColors = [
            'AGENDADO' => '#28a745',
            'EMERGENCIAL' => '#dc3545',
            'REAGENDADO' => '#ffc107'
        ];

        foreach ($agendamentos as $agendamento) {
            $eventos[] = [
                'id' => $agendamento['id'],
                'title' => $agendamento['veiculo'],
                'start' => $agendamento['data_agendamento'],
                'backgroundColor' => $tipoColors[$agendamento['tipo']] ?? '#6c757d',
                'borderColor' => $tipoColors[$agendamento['tipo']] ?? '#6c757d',
                'extendedProps' => [
                    'empreiteira' => $agendamento['empreiteira'],
                    'tipo' => $agendamento['tipo'],
                    'identificador' => $agendamento['identificador'],
                    'confirmado' => $agendamento['confirmacao'] ? true : false
                ]
            ];
        }

        return $eventos;
    }

    private function prepararDadosGraficoTipos($dados)
    {
        $labels = [];
        $values = [];
        $colors = [
            'AGENDADO' => '#28a745',
            'EMERGENCIAL' => '#dc3545',
            'REAGENDADO' => '#ffc107'
        ];
        $backgroundColors = [];

        foreach ($dados as $item) {
            $labels[] = $item['tipo'] ?? '';
            $values[] = $item['total'] ?? 0;
            $backgroundColors[] = $colors[$item['tipo'] ?? ''] ?? '#007bff';
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'backgroundColors' => $backgroundColors
        ];
    }

    private function prepararDadosGraficoEmpreiteiras($dados)
    {
        $labels = [];
        $values = [];

        foreach ($dados as $item) {
            $labels[] = $item['empreiteira'] ?? '';
            $values[] = $item['total'] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Prepara os dados para o gráfico de agendamentos por dia da semana
     * 
     * @param array $dados Dados brutos do modelo
     * @return array Dados formatados para o gráfico
     */
    private function prepararDadosGraficoDiasSemana($dados)
    {
        $diasOrdenados = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $values = array_fill(0, 7, 0); // Inicializa com zeros

        foreach ($dados as $item) {
            $index = (int)($item['dia_semana'] ?? 1) - 1; // Ajusta para índice 0-6
            if ($index >= 0 && $index < 7) {
                $values[$index] = (int)($item['total'] ?? 0);
            }
        }

        return [
            'labels' => $diasOrdenados,
            'values' => $values
        ];
    }

    /**
     * Prepara os dados para o gráfico de agendamentos por hora
     * 
     * @param array $dados Dados brutos do modelo
     * @return array Dados formatados para o gráfico
     */
    private function prepararDadosGraficoHoras($dados)
    {
        $labels = [];
        $values = [];

        // Inicializar todas as horas com zero
        for ($i = 8; $i < 18; $i++) {
            $labels[] = sprintf("%02d:00", $i);
            $values[] = 0;
        }

        // Preencher com dados reais
        foreach ($dados as $item) {
            $hora = (int)($item['hora'] ?? 0);
            if ($hora >= 8 && $hora < 18) {
                $values[$hora - 8] = (int)($item['total'] ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Prepara os dados para o gráfico de agendamentos por mês
     * 
     * @param array $dados Dados brutos do modelo
     * @return array Dados formatados para o gráfico
     */
    private function prepararDadosGraficoMeses($dados)
    {
        $labels = [];
        $values = [];

        foreach ($dados as $item) {
            $labels[] = $item['periodo'] ?? '';
            $values[] = (int)($item['total'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}
