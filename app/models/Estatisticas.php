<?php
class Estatisticas
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function totalAgendamentos()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM agendamentos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function agendamentosHoje()
    {
        $hoje = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) = :data
        ");
        $stmt->bindParam(':data', $hoje);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function agendamentosProximaSemana()
    {
        $hoje = date('Y-m-d');
        $proximaSemana = date('Y-m-d', strtotime('+7 days'));

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :hoje AND :proxima_semana
        ");
        $stmt->bindParam(':hoje', $hoje);
        $stmt->bindParam(':proxima_semana', $proximaSemana);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function agendamentosPorTipo()
    {
        $stmt = $this->db->query("
            SELECT tipo, COUNT(*) as total 
            FROM agendamentos 
            GROUP BY tipo 
            ORDER BY total DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function agendamentosPorEmpreiteira()
    {
        $stmt = $this->db->query("
            SELECT empreiteira, COUNT(*) as total 
            FROM agendamentos 
            GROUP BY empreiteira 
            ORDER BY total DESC 
            LIMIT 10
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Retorna a contagem de agendamentos por dia da semana
     * 
     * @return array Array com dias da semana e contagem de agendamentos
     */
    public function agendamentosPorDiaDaSemana()
    {
        try {
            $stmt = $this->db->query("
            SELECT 
                DAYOFWEEK(data_agendamento) as dia_semana, 
                COUNT(*) as total 
            FROM agendamentos 
            GROUP BY dia_semana 
            ORDER BY dia_semana
        ");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

            // Converter números para nomes dos dias
            $diasSemana = [
                1 => 'Domingo',
                2 => 'Segunda',
                3 => 'Terça',
                4 => 'Quarta',
                5 => 'Quinta',
                6 => 'Sexta',
                7 => 'Sábado'
            ];

            foreach ($result as &$row) {
                $row['nome_dia'] = $diasSemana[$row['dia_semana']] ?? '';
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamentos por dia da semana: " . $e->getMessage());
            return [];
        }
    }

    public function agendamentosPorHora()
    {
        $stmt = $this->db->query("
            SELECT 
                HOUR(data_agendamento) as hora, 
                COUNT(*) as total 
            FROM agendamentos 
            GROUP BY hora 
            ORDER BY hora
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function agendamentosPorMes()
    {
        $stmt = $this->db->query("
            SELECT 
                YEAR(data_agendamento) as ano,
                MONTH(data_agendamento) as mes, 
                COUNT(*) as total 
            FROM agendamentos 
            WHERE data_agendamento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY ano, mes 
            ORDER BY ano, mes
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

        // Converter números para nomes dos meses
        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        foreach ($result as &$row) {
            $row['nome_mes'] = $meses[$row['mes']] ?? '';
            $row['periodo'] = ($row['nome_mes'] ?? '') . '/' . ($row['ano'] ?? '');
        }

        return $result;
    }

    public function taxaConfirmacao()
    {
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN confirmacao = 1 THEN 1 ELSE 0 END) as confirmados,
                COUNT(*) as total
            FROM agendamentos
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ?? ['confirmados' => 0, 'total' => 0];

        if (($result['total'] ?? 0) > 0) {
            $result['taxa'] = round(($result['confirmados'] / $result['total']) * 100, 2);
        } else {
            $result['taxa'] = 0;
        }

        return $result;
    }

    /**
     * Retorna os horários disponíveis para uma data específica
     * 
     * @param string $data Data no formato Y-m-d
     * @param int|null $excluirId ID do agendamento a ser excluído da verificação (para edição)
     * @return array Array com horários e sua disponibilidade
     */
    public function horariosDisponiveis($data, $excluirId = null)
    {
        try {
            // Horários de funcionamento (8h às 18h, intervalos de 30 minutos)
            $horariosDisponiveis = [];
            $horariosOcupados = [];

            // Gerar todos os horários possíveis
            for ($hora = 8; $hora < 18; $hora++) {
                $horariosDisponiveis[] = sprintf("%02d:00", $hora);
                $horariosDisponiveis[] = sprintf("%02d:30", $hora);
            }

            // Buscar horários já agendados para a data
            $sql = "
            SELECT 
                TIME_FORMAT(data_agendamento, '%H:%i') as horario,
                duracao
            FROM agendamentos
            WHERE DATE(data_agendamento) = :data
        ";

            // Se estiver editando um agendamento, excluir ele da verificação
            if ($excluirId) {
                $sql .= " AND id != :excluir_id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':data', $data);

            if ($excluirId) {
                $stmt->bindParam(':excluir_id', $excluirId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

            // Marcar horários ocupados considerando a duração
            foreach ($agendamentos as $agendamento) {
                $horarioInicial = $agendamento['horario'] ?? '';
                $duracao = intval($agendamento['duracao'] ?? 30);

                // Calcular quantos slots de 30 minutos são necessários
                $numSlots = ceil($duracao / 30);

                // Extrair hora e minuto iniciais
                list($horaIni, $minutoIni) = explode(':', $horarioInicial);
                $horaIni = intval($horaIni);
                $minutoIni = intval($minutoIni);

                // Marcar o horário inicial como ocupado
                $horariosOcupados[] = $horarioInicial;

                // Marcar os horários subsequentes como ocupados
                $horaAtual = $horaIni;
                $minutoAtual = $minutoIni;

                for ($i = 1; $i < $numSlots; $i++) {
                    $minutoAtual += 30;

                    if ($minutoAtual >= 60) {
                        $horaAtual += 1;
                        $minutoAtual -= 60;
                    }

                    // Formatar a hora
                    $horaFormatada = sprintf("%02d:%02d", $horaAtual, $minutoAtual);
                    $horariosOcupados[] = $horaFormatada;
                }
            }

            // Buscar horários marcados como indisponíveis na tabela horarios_disponiveis
            $sql = "
            SELECT TIME_FORMAT(hora, '%H:%i') as horario
            FROM horarios_disponiveis
            WHERE data = :data AND disponivel = 0
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':data', $data);
            $stmt->execute();

            $horariosIndisponiveis = $stmt->fetchAll(PDO::FETCH_COLUMN) ?? [];

            // Adicionar horários indisponíveis à lista de ocupados
            $horariosOcupados = array_merge($horariosOcupados, $horariosIndisponiveis);

            $resultado = [];
            foreach ($horariosDisponiveis as $horario) {
                $resultado[] = [
                    'horario' => $horario,
                    'disponivel' => !in_array($horario, $horariosOcupados)
                ];
            }

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar horários disponíveis: " . $e->getMessage());
            return [];
        }
    }

    public function ultimosAgendamentos($limite = 5)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.nome as nome_usuario
            FROM agendamentos a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            ORDER BY a.data_agendamento DESC
            LIMIT :limite
        ");
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Retorna o total de agendamentos no período especificado
     */
    public function totalAgendamentosPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro ao buscar total de agendamentos por período: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retorna a contagem de agendamentos por tipo no período especificado
     */
    public function agendamentosPorTipoPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
            GROUP BY tipo 
            ORDER BY total DESC
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamentos por tipo e período: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna a contagem de agendamentos por dia da semana no período especificado
     */
    public function agendamentosPorDiaDaSemanaPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                DAYOFWEEK(data_agendamento) as dia_semana, 
                COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
            GROUP BY dia_semana 
            ORDER BY dia_semana
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

            // Converter números para nomes dos dias
            $diasSemana = [
                1 => 'Domingo',
                2 => 'Segunda',
                3 => 'Terça',
                4 => 'Quarta',
                5 => 'Quinta',
                6 => 'Sexta',
                7 => 'Sábado'
            ];

            foreach ($result as &$row) {
                $row['nome_dia'] = $diasSemana[$row['dia_semana']] ?? '';
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamentos por dia da semana e período: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna a contagem de agendamentos por hora no período especificado
     */
    public function agendamentosPorHoraPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                HOUR(data_agendamento) as hora, 
                COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
            GROUP BY hora 
            ORDER BY hora
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamentos por hora e período: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna a contagem de agendamentos por mês no período especificado
     */
    public function agendamentosPorMesPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                YEAR(data_agendamento) as ano,
                MONTH(data_agendamento) as mes, 
                COUNT(*) as total 
            FROM agendamentos 
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
            GROUP BY ano, mes 
            ORDER BY ano, mes
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

            // Converter números para nomes dos meses
            $meses = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro'
            ];

            foreach ($result as &$row) {
                $row['nome_mes'] = $meses[$row['mes']] ?? '';
                $row['periodo'] = ($row['nome_mes'] ?? '') . '/' . ($row['ano'] ?? '');
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao buscar agendamentos por mês e período: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna a taxa de confirmação no período especificado
     */
    public function taxaConfirmacaoPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN confirmacao = 1 THEN 1 ELSE 0 END) as confirmados,
                COUNT(*) as total
            FROM agendamentos
            WHERE DATE(data_agendamento) BETWEEN :data_inicio AND :data_fim
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC) ?? ['confirmados' => 0, 'total' => 0];

            if (($result['total'] ?? 0) > 0) {
                $result['taxa'] = round(($result['confirmados'] / $result['total']) * 100, 2);
            } else {
                $result['taxa'] = 0;
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao buscar taxa de confirmação por período: " . $e->getMessage());
            return ['confirmados' => 0, 'total' => 0, 'taxa' => 0];
        }
    }
}
