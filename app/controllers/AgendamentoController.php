<?php
class AgendamentoController
{
    private $agendamentoModel;
    private $estatisticasModel;
    private $horariosModel;

    public function __construct()
    {
        $this->agendamentoModel = new Agendamento();
        $this->estatisticasModel = new Estatisticas();
        $this->horariosModel = new Horarios();
    }

    public function listar()
    {
        // Todos os usuários podem visualizar agendamentos
        $agendamentos = $this->agendamentoModel->listarTodos();
        $titulo = "Lista de Agendamentos";

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

        // Obter parâmetros da URL
        $data = $_GET['data'] ?? date('Y-m-d');
        $hora = $_GET['hora'] ?? '08:00';

        // Verificar se o horário está disponível
        $horarios = $this->estatisticasModel->horariosDisponiveis($data);
        $horarioDisponivel = false;

        foreach ($horarios as $horario) {
            if ($horario['horario'] === $hora && $horario['disponivel']) {
                $horarioDisponivel = true;
                break;
            }
        }

        // Se o horário não estiver disponível e o usuário não for admin, redirecionar
        if (!$horarioDisponivel && $_SESSION['usuario_tipo'] !== 'admin') {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'O horário selecionado não está disponível.'
            ];
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        $titulo = 'Novo Agendamento - Sistema de Agendamento';

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

    public function salvar()
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
            'duracao' => $_POST['duracao'] ?? 30,
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

        // Verificar se todos os horários estão disponíveis
        $dataHora = explode(' ', $dados['data_agendamento']);
        $data = $dataHora[0];
        $horaInicial = $dataHora[1];
        $duracao = intval($dados['duracao']);

        if (!$this->verificarDisponibilidadeHorarios($data, $horaInicial, $duracao)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Alguns dos horários selecionados não estão disponíveis. Ajuste a duração ou selecione outro horário inicial.'
            ];
            header('Location: /projeto-agendamento/public/agendamentos/criar?data=' . $data . '&hora=' . $horaInicial);
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
            header('Location: /projeto-agendamento/public/agendamentos/criar?data=' . $data . '&hora=' . $horaInicial);
        }
        exit;
    }

    /**
     * Verifica se todos os horários necessários para um agendamento estão disponíveis
     * 
     * @param string $data Data no formato Y-m-d
     * @param string $horaInicial Hora inicial no formato H:i
     * @param int $duracao Duração em minutos
     * @return bool True se todos os horários estão disponíveis
     */
    private function verificarDisponibilidadeHorarios($data, $horaInicial, $duracao)
    {
        // Obter todos os horários disponíveis para a data
        $horariosDisponiveis = $this->estatisticasModel->horariosDisponiveis($data);

        // Calcular quantos slots de 30 minutos são necessários
        $numSlots = ceil($duracao / 30);

        // Extrair hora e minuto iniciais
        list($horaIni, $minutoIni) = explode(':', $horaInicial);
        $horaIni = intval($horaIni);
        $minutoIni = intval($minutoIni);

        // Verificar disponibilidade do horário inicial
        $horarioInicialDisponivel = false;
        foreach ($horariosDisponiveis as $horario) {
            if ($horario['horario'] === $horaInicial && $horario['disponivel']) {
                $horarioInicialDisponivel = true;
                break;
            }
        }

        if (!$horarioInicialDisponivel) {
            return false;
        }

        // Verificar disponibilidade dos horários subsequentes
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

            // Verificar se o horário está disponível
            $disponivel = false;
            foreach ($horariosDisponiveis as $horario) {
                if ($horario['horario'] === $horaFormatada && $horario['disponivel']) {
                    $disponivel = true;
                    break;
                }
            }

            if (!$disponivel) {
                return false;
            }
        }

        return true;
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

        $titulo = 'Editar Agendamento';

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
