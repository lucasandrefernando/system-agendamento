<?php
class DashboardController
{
    private $estatisticasModel;
    private $agendamentoModel;

    public function __construct()
    {
        $this->estatisticasModel = new Estatisticas();
        $this->agendamentoModel = new Agendamento();
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
        $ultimos_agendamentos = $this->estatisticasModel->ultimosAgendamentos(5);
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
        $horarios = $this->estatisticasModel->horariosDisponiveis($data, $excluirId);

        // Retornar como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'data' => $data,
            'horarios' => $horarios
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
