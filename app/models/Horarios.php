<?php
class Horarios
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Atualiza a disponibilidade de um horário
     * @param string $data Data no formato Y-m-d
     * @param string $hora Hora no formato H:i
     * @param bool $disponivel Se o horário está disponível
     * @return bool Resultado da operação
     */
    public function atualizarDisponibilidadeHorario($data, $hora, $disponivel)
    {
        try {
            // Verificar se já existe um registro para esta data/hora
            $sql = "SELECT id FROM horarios_disponiveis WHERE data = :data AND hora = :hora";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':hora', $hora);
            $stmt->execute();
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro) {
                // Atualizar registro existente
                $sql = "UPDATE horarios_disponiveis SET disponivel = :disponivel WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $disponivelInt = $disponivel ? 1 : 0;
                $stmt->bindParam(':disponivel', $disponivelInt, PDO::PARAM_INT);
                $stmt->bindParam(':id', $registro['id'], PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                // Criar novo registro
                $sql = "INSERT INTO horarios_disponiveis (data, hora, disponivel) VALUES (:data, :hora, :disponivel)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':data', $data);
                $stmt->bindParam(':hora', $hora);
                $disponivelInt = $disponivel ? 1 : 0;
                $stmt->bindParam(':disponivel', $disponivelInt, PDO::PARAM_INT);
                return $stmt->execute();
            }
        } catch (PDOException $e) {
            error_log("Erro ao atualizar disponibilidade: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém a disponibilidade de um horário específico
     * @param string $data Data no formato Y-m-d
     * @param string $hora Hora no formato H:i
     * @return bool|null true se disponível, false se ocupado, null se não existir
     */
    public function verificarDisponibilidade($data, $hora)
    {
        try {
            $sql = "SELECT disponivel FROM horarios_disponiveis WHERE data = :data AND hora = :hora";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':hora', $hora);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return (bool) $resultado['disponivel'];
            }

            return null; // Horário não encontrado
        } catch (PDOException $e) {
            error_log("Erro ao verificar disponibilidade: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista todos os horários disponíveis para uma data
     * @param string $data Data no formato Y-m-d
     * @param int|null $excluirId ID de agendamento a ser excluído da verificação
     * @return array Lista de horários com status de disponibilidade
     */
    public function listarHorariosPorData($data, $excluirId = null)
    {
        try {
            // Primeiro, criar lista de todos os horários padrão (8h às 18h, a cada 30 min)
            $horarios = [];
            $horaInicio = 8;
            $horaFim = 18;

            for ($hora = $horaInicio; $hora < $horaFim; $hora++) {
                for ($minuto = 0; $minuto < 60; $minuto += 30) {
                    $horarioStr = sprintf("%02d:%02d", $hora, $minuto);
                    $horarios[] = [
                        'horario' => $horarioStr,
                        'disponivel' => true // Por padrão, todos estão disponíveis
                    ];
                }
            }

            // Agora, buscar os horários que estão marcados como indisponíveis no banco
            $sql = "SELECT hora, disponivel FROM horarios_disponiveis WHERE data = :data";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':data', $data);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Atualizar a disponibilidade com base nos registros do banco
            foreach ($registros as $registro) {
                foreach ($horarios as &$horario) {
                    if ($horario['horario'] === $registro['hora']) {
                        $horario['disponivel'] = (bool) $registro['disponivel'];
                        break;
                    }
                }
            }

            // Verificar agendamentos existentes para marcar como indisponíveis
            $sql = "SELECT TIME_FORMAT(data_agendamento, '%H:%i') as hora 
                    FROM agendamentos 
                    WHERE DATE(data_agendamento) = :data";

            if ($excluirId) {
                $sql .= " AND id != :excluir_id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':data', $data);
                $stmt->bindParam(':excluir_id', $excluirId, PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':data', $data);
            }

            $stmt->execute();
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Marcar horários com agendamentos como indisponíveis
            foreach ($agendamentos as $agendamento) {
                $horaAgendamento = $agendamento['hora'];

                foreach ($horarios as &$horario) {
                    if ($horario['horario'] === $horaAgendamento) {
                        $horario['disponivel'] = false;
                        break;
                    }
                }
            }

            return $horarios;
        } catch (PDOException $e) {
            error_log("Erro ao listar horários: " . $e->getMessage());
            return [];
        }
    }
}
