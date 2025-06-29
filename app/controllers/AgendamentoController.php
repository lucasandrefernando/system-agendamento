<?php
class AgendamentoController
{
    private $agendamentoModel;

    public function __construct()
    {
        $this->agendamentoModel = new Agendamento();
    }

    public function listar()
    {
        // Todos os usuários podem visualizar agendamentos
        $agendamentos = $this->agendamentoModel->listarTodos();

        ob_start();
        require_once __DIR__ . '/../views/agendamentos/listar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function criarForm()
    {
        // Apenas usuários admin e medio podem criar agendamentos
        if ($_SESSION['usuario_tipo'] === 'comum') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para criar agendamentos.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        ob_start();
        require_once __DIR__ . '/../views/agendamentos/criar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function criar()
    {
        // Apenas usuários admin e medio podem criar agendamentos
        if ($_SESSION['usuario_tipo'] === 'comum') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para criar agendamentos.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        $dados = [
            'veiculo' => $_POST['veiculo'] ?? '',
            'empreiteira' => $_POST['empreiteira'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'AGENDADO',
            'identificador' => $_POST['identificador'] ?? '',
            'documento' => $_POST['documento'] ?? '',
            'confirmacao' => isset($_POST['confirmacao']) ? 1 : 0,
            'data_agendamento' => $_POST['data_agendamento'] ?? date('Y-m-d H:i:s'),
            'usuario_id' => $_SESSION['usuario_id']
        ];

        // Validação
        if (empty($dados['veiculo']) || empty($dados['empreiteira']) || empty($dados['identificador'])) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Por favor, preencha todos os campos obrigatórios.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos/criar');
            exit;
        }

        if ($this->agendamentoModel->criar($dados)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Agendamento criado com sucesso!'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao criar agendamento. Tente novamente.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos/criar');
        }
        exit;
    }

    public function editarForm($id)
    {
        // Apenas usuários admin podem editar agendamentos
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para editar agendamentos.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        $agendamento = $this->agendamentoModel->buscarPorId($id);

        if (!$agendamento) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Agendamento não encontrado.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        ob_start();
        require_once __DIR__ . '/../views/agendamentos/editar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function editar($id)
    {
        // Apenas usuários admin podem editar agendamentos
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para editar agendamentos.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        $dados = [
            'veiculo' => $_POST['veiculo'] ?? '',
            'empreiteira' => $_POST['empreiteira'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'AGENDADO',
            'identificador' => $_POST['identificador'] ?? '',
            'documento' => $_POST['documento'] ?? '',
            'confirmacao' => isset($_POST['confirmacao']) ? 1 : 0,
            'data_agendamento' => $_POST['data_agendamento'] ?? date('Y-m-d H:i:s')
        ];

        // Validação
        if (empty($dados['veiculo']) || empty($dados['empreiteira']) || empty($dados['identificador'])) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Por favor, preencha todos os campos obrigatórios.'
            ];
            header("Location: /projeto-agendamento/public/agendamentos/editar/{$id}");
            exit;
        }

        if ($this->agendamentoModel->atualizar($id, $dados)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Agendamento atualizado com sucesso!'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao atualizar agendamento. Tente novamente.'
            ];
            header("Location: /projeto-agendamento/public/agendamentos/editar/{$id}");
        }
        exit;
    }

    public function excluir($id)
    {
        // Apenas usuários admin podem excluir agendamentos
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para excluir agendamentos.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos');
            exit;
        }

        if ($this->agendamentoModel->excluir($id)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Agendamento excluído com sucesso!'
            ];
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao excluir agendamento. Tente novamente.'
            ];
        }

        header('Location: /projeto-agendamento/public/agendamentos');
        exit;
    }
}
