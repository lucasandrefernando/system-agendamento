<?php
class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function listar()
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para acessar esta página.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        $usuarios = $this->usuarioModel->listarTodos();

        ob_start();
        require_once __DIR__ . '/../views/usuarios/listar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function criarForm()
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para acessar esta página.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        ob_start();
        require_once __DIR__ . '/../views/usuarios/criar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function criar()
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para realizar esta ação.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'email' => $_POST['email'] ?? '',
            'cpf' => $_POST['cpf'] ?? '',
            'tipo_usuario' => $_POST['tipo_usuario'] ?? 'comum'
        ];

        // Validação
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['cpf'])) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Por favor, preencha todos os campos obrigatórios.'
            ];
            header('Location: /projeto-agendamento/public/usuarios/criar');
            exit;
        }

        // Validar formato do CPF
        if (!preg_match('/^\d{11}$/', preg_replace('/[^0-9]/', '', $dados['cpf']))) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'CPF inválido. Informe apenas os números.'
            ];
            header('Location: /projeto-agendamento/public/usuarios/criar');
            exit;
        }

        // Formatar CPF
        $dados['cpf'] = preg_replace('/[^0-9]/', '', $dados['cpf']);

        if ($this->usuarioModel->criar($dados)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Usuário criado com sucesso! A senha inicial são os 4 primeiros dígitos do CPF.'
            ];
            header('Location: /projeto-agendamento/public/usuarios');
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao criar usuário. Tente novamente.'
            ];
            header('Location: /projeto-agendamento/public/usuarios/criar');
        }
        exit;
    }

    public function editarForm($id)
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para acessar esta página.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        $usuario = $this->usuarioModel->buscarPorId($id);

        if (!$usuario) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Usuário não encontrado.'
            ];
            header('Location: /projeto-agendamento/public/usuarios');
            exit;
        }

        ob_start();
        require_once __DIR__ . '/../views/usuarios/editar.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }

    public function editar($id)
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para realizar esta ação.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'email' => $_POST['email'] ?? '',
            'cpf' => $_POST['cpf'] ?? '',
            'tipo_usuario' => $_POST['tipo_usuario'] ?? 'comum'
        ];

        // Validação
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['cpf'])) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Por favor, preencha todos os campos obrigatórios.'
            ];
            header("Location: /projeto-agendamento/public/usuarios/editar/{$id}");
            exit;
        }

        // Validar formato do CPF
        if (!preg_match('/^\d{11}$/', preg_replace('/[^0-9]/', '', $dados['cpf']))) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'CPF inválido. Informe apenas os números.'
            ];
            header("Location: /projeto-agendamento/public/usuarios/editar/{$id}");
            exit;
        }

        // Formatar CPF
        $dados['cpf'] = preg_replace('/[^0-9]/', '', $dados['cpf']);

        if ($this->usuarioModel->atualizar($id, $dados)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Usuário atualizado com sucesso!'
            ];
            header('Location: /projeto-agendamento/public/usuarios');
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Erro ao atualizar usuário. Tente novamente.'
            ];
            header("Location: /projeto-agendamento/public/usuarios/editar/{$id}");
        }
        exit;
    }

    public function excluir($id)
    {
        // Verificar se o usuário atual é administrador
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Você não tem permissão para realizar esta ação.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        if ($this->usuarioModel->excluir($id)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => 'Usuário excluído com sucesso!'
            ];
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Não foi possível excluir o usuário. Verifique se ele possui agendamentos.'
            ];
        }

        header('Location: /projeto-agendamento/public/usuarios');
        exit;
    }
}
