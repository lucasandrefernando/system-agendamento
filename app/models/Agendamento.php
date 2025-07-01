<?php
class Agendamento
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listarTodos()
    {
        $stmt = $this->db->query("SELECT * FROM agendamentos ORDER BY data_agendamento DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM agendamentos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo agendamento
     */
    public function criar($dados)
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO agendamentos 
            (veiculo, empreiteira, tipo, identificador, documento, confirmacao, data_agendamento, duracao, usuario_id) 
            VALUES 
            (:veiculo, :empreiteira, :tipo, :identificador, :documento, :confirmacao, :data_agendamento, :duracao, :usuario_id)
        ");

            $confirmacao = isset($dados['confirmacao']) ? 1 : 0;
            $duracao = isset($dados['duracao']) ? intval($dados['duracao']) : 30;

            $stmt->bindParam(':veiculo', $dados['veiculo']);
            $stmt->bindParam(':empreiteira', $dados['empreiteira']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            $stmt->bindParam(':identificador', $dados['identificador']);
            $stmt->bindParam(':documento', $dados['documento']);
            $stmt->bindParam(':confirmacao', $confirmacao, PDO::PARAM_INT);
            $stmt->bindParam(':data_agendamento', $dados['data_agendamento']);
            $stmt->bindParam(':duracao', $duracao, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $dados['usuario_id']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar agendamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um agendamento existente
     */
    public function atualizar($id, $dados)
    {
        try {
            $stmt = $this->db->prepare("
            UPDATE agendamentos 
            SET veiculo = :veiculo, 
                empreiteira = :empreiteira, 
                tipo = :tipo, 
                identificador = :identificador, 
                documento = :documento, 
                confirmacao = :confirmacao, 
                data_agendamento = :data_agendamento,
                duracao = :duracao
            WHERE id = :id
        ");

            $confirmacao = isset($dados['confirmacao']) ? 1 : 0;
            $duracao = isset($dados['duracao']) ? intval($dados['duracao']) : 30;

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':veiculo', $dados['veiculo']);
            $stmt->bindParam(':empreiteira', $dados['empreiteira']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            $stmt->bindParam(':identificador', $dados['identificador']);
            $stmt->bindParam(':documento', $dados['documento']);
            $stmt->bindParam(':confirmacao', $confirmacao, PDO::PARAM_INT);
            $stmt->bindParam(':data_agendamento', $dados['data_agendamento']);
            $stmt->bindParam(':duracao', $duracao, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar agendamento: " . $e->getMessage());
            return false;
        }
    }

    public function excluir($id)
    {
        $stmt = $this->db->prepare("DELETE FROM agendamentos WHERE id = :id");
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Lista agendamentos por período
     * 
     * @param string $dataInicio Data inicial no formato Y-m-d
     * @param string $dataFim Data final no formato Y-m-d
     * @return array Lista de agendamentos no período
     */
    public function listarPorPeriodo($dataInicio, $dataFim)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT a.*, u.nome as nome_usuario
            FROM agendamentos a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE DATE(a.data_agendamento) BETWEEN :data_inicio AND :data_fim
            ORDER BY a.data_agendamento
        ");
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao listar agendamentos por período: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista todas as empreiteiras distintas
     * @return array Lista de empreiteiras
     */
    public function listarEmpreiteiras()
    {
        try {
            $stmt = $this->db->query("
            SELECT DISTINCT empreiteira 
            FROM agendamentos 
            WHERE empreiteira IS NOT NULL AND empreiteira != '' 
            ORDER BY empreiteira
        ");
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?? [];
        } catch (PDOException $e) {
            error_log("Erro ao listar empreiteiras: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Confirma um agendamento
     * @param int $id ID do agendamento
     * @return bool Resultado da operação
     */
    public function confirmar($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE agendamentos SET confirmacao = 1 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao confirmar agendamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancela a confirmação de um agendamento
     * @param int $id ID do agendamento
     * @return bool Resultado da operação
     */
    public function cancelar($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE agendamentos SET confirmacao = 0 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao cancelar agendamento: " . $e->getMessage());
            return false;
        }
    }
}
