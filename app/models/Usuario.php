<?php
class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function autenticar($usuario, $senha)
    {
        try {
            // Buscar usuário pelo formato nome.sobrenome
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Verificar se a senha corresponde aos 4 primeiros dígitos do CPF
                $cpfDigits = substr(preg_replace('/[^0-9]/', '', $usuario['cpf']), 0, 4);

                // Para depuração
                error_log("CPF: " . $usuario['cpf']);
                error_log("Primeiros 4 dígitos: " . $cpfDigits);
                error_log("Senha fornecida: " . $senha);

                if ($senha === $cpfDigits) {
                    return $usuario;
                }
            }

            return false;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos()
    {
        $stmt = $this->db->query("SELECT * FROM usuarios ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados)
    {
        try {
            // Gerar nome de usuário no formato nome.sobrenome
            $nomes = explode(' ', $dados['nome']);
            $primeiroNome = strtolower($nomes[0]);
            $ultimoNome = strtolower(end($nomes));
            $usuario = $primeiroNome . '.' . $ultimoNome;

            // Verificar se o usuário já existe
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                // Se já existir, adicionar um número ao final
                $contador = 1;
                $novoUsuario = $usuario . $contador;

                while (true) {
                    $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
                    $stmt->bindParam(':usuario', $novoUsuario);
                    $stmt->execute();

                    if ($stmt->fetchColumn() == 0) {
                        $usuario = $novoUsuario;
                        break;
                    }

                    $contador++;
                    $novoUsuario = $usuario . $contador;
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO usuarios 
                (nome, email, cpf, usuario, tipo_usuario) 
                VALUES 
                (:nome, :email, :cpf, :usuario, :tipo_usuario)
            ");

            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':cpf', $dados['cpf']);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':tipo_usuario', $dados['tipo_usuario']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar($id, $dados)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET nome = :nome, 
                    email = :email, 
                    cpf = :cpf, 
                    tipo_usuario = :tipo_usuario
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':cpf', $dados['cpf']);
            $stmt->bindParam(':tipo_usuario', $dados['tipo_usuario']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function excluir($id)
    {
        try {
            // Verificar se o usuário tem agendamentos
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM agendamentos WHERE usuario_id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                return false; // Não pode excluir usuário com agendamentos
            }

            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            return false;
        }
    }
}
