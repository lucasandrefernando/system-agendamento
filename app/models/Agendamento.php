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

    public function criar($dados)
    {
        $stmt = $this->db->prepare("
            INSERT INTO agendamentos 
            (veiculo, empreiteira, tipo, identificador, documento, confirmacao, data_agendamento, usuario_id) 
            VALUES 
            (:veiculo, :empreiteira, :tipo, :identificador, :documento, :confirmacao, :data_agendamento, :usuario_id)
        ");

        $confirmacao = isset($dados['confirmacao']) ? 1 : 0;

        $stmt->bindParam(':veiculo', $dados['veiculo']);
        $stmt->bindParam(':empreiteira', $dados['empreiteira']);
        $stmt->bindParam(':tipo', $dados['tipo']);
        $stmt->bindParam(':identificador', $dados['identificador']);
        $stmt->bindParam(':documento', $dados['documento']);
        $stmt->bindParam(':confirmacao', $confirmacao, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $dados['data_agendamento']);
        $stmt->bindParam(':usuario_id', $dados['usuario_id']);

        return $stmt->execute();
    }

    public function atualizar($id, $dados)
    {
        $stmt = $this->db->prepare("
            UPDATE agendamentos 
            SET veiculo = :veiculo, 
                empreiteira = :empreiteira, 
                tipo = :tipo, 
                identificador = :identificador, 
                documento = :documento, 
                confirmacao = :confirmacao, 
                data_agendamento = :data_agendamento
            WHERE id = :id
        ");

        $confirmacao = isset($dados['confirmacao']) ? 1 : 0;

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':veiculo', $dados['veiculo']);
        $stmt->bindParam(':empreiteira', $dados['empreiteira']);
        $stmt->bindParam(':tipo', $dados['tipo']);
        $stmt->bindParam(':identificador', $dados['identificador']);
        $stmt->bindParam(':documento', $dados['documento']);
        $stmt->bindParam(':confirmacao', $confirmacao, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $dados['data_agendamento']);

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $stmt = $this->db->prepare("DELETE FROM agendamentos WHERE id = :id");
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
