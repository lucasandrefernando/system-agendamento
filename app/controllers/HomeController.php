<?php
class HomeController
{
    private $agendamentoModel;

    public function __construct()
    {
        $this->agendamentoModel = new Agendamento();
    }

    public function index()
    {
        // Busca os agendamentos para exibir no dashboard
        $agendamentos = $this->agendamentoModel->listarTodos();

        // Limita a 5 agendamentos mais recentes
        $agendamentos = array_slice($agendamentos, 0, 5);

        // Carrega a view
        ob_start();
        require_once __DIR__ . '/../views/home/index.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/app.php';
    }
}
