<?php
// Configuração de erro para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregamento dos arquivos necessários
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Agendamento.php';
require_once __DIR__ . '/../app/models/Estatisticas.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AgendamentoController.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

// Inicia a sessão
session_start();

// Implementação básica do roteamento
$uri = $_SERVER['REQUEST_URI'];
$baseUri = '/projeto-agendamento/public';
$uri = str_replace($baseUri, '', $uri);
$method = $_SERVER['REQUEST_METHOD'];

// Se a URI estiver vazia, redireciona para a raiz
if ($uri == '') {
    $uri = '/';
}

// Middleware de autenticação
function checkAuth($uri)
{
    $publicRoutes = ['/', '/login'];

    if (!isset($_SESSION['usuario_id']) && !in_array($uri, $publicRoutes)) {
        header('Location: /projeto-agendamento/public/');
        exit;
    }
}

// Processar a rota
$routeFound = false;

// Verificar rotas específicas com base no método HTTP
if ($uri === '/agendamentos/criar' && $method === 'POST') {
    checkAuth($uri);
    $controller = new AgendamentoController();
    $controller->criar();
    $routeFound = true;
} elseif ($uri === '/usuarios/criar' && $method === 'POST') {
    checkAuth($uri);
    $controller = new UsuarioController();
    $controller->criar();
    $routeFound = true;
} elseif ($uri === '/login' && $method === 'POST') {
    $controller = new AuthController();
    $controller->login();
    $routeFound = true;
} elseif (preg_match('#^/agendamentos/editar/(\d+)$#', $uri, $matches) && $method === 'POST') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new AgendamentoController();
    $controller->editar($id);
    $routeFound = true;
} elseif (preg_match('#^/usuarios/editar/(\d+)$#', $uri, $matches) && $method === 'POST') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new UsuarioController();
    $controller->editar($id);
    $routeFound = true;
} elseif (preg_match('#^/agendamentos/excluir/(\d+)$#', $uri, $matches) && $method === 'POST') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new AgendamentoController();
    $controller->excluir($id);
    $routeFound = true;
} elseif (preg_match('#^/usuarios/excluir/(\d+)$#', $uri, $matches) && $method === 'POST') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new UsuarioController();
    $controller->excluir($id);
    $routeFound = true;
} elseif ($uri === '/') {
    $controller = new AuthController();
    $controller->loginForm();
    $routeFound = true;
} elseif ($uri === '/logout') {
    checkAuth($uri);
    $controller = new AuthController();
    $controller->logout();
    $routeFound = true;
} elseif ($uri === '/dashboard') {
    checkAuth($uri);
    $controller = new DashboardController();
    $controller->index();
    $routeFound = true;
} elseif ($uri === '/dashboard/indicadores') {
    checkAuth($uri);
    $controller = new DashboardController();
    $controller->index();
    $routeFound = true;
} elseif (strpos($uri, '/dashboard/horarios-disponiveis') === 0) {
    checkAuth($uri);
    $controller = new DashboardController();
    $controller->horariosDisponiveis();
    $routeFound = true;
} elseif (preg_match('/^\/dashboard\/horarios-disponiveis/', $uri)) {
    checkAuth($uri);
    $controller = new DashboardController();
    $controller->horariosDisponiveis();
    $routeFound = true;
} elseif ($uri === '/agendamentos') {
    checkAuth($uri);
    $controller = new AgendamentoController();
    $controller->listar();
    $routeFound = true;
} elseif ($uri === '/agendamentos/criar' && $method === 'GET') {
    checkAuth($uri);
    $controller = new AgendamentoController();
    $controller->criarForm();
    $routeFound = true;
} elseif (preg_match('#^/agendamentos/editar/(\d+)$#', $uri, $matches) && $method === 'GET') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new AgendamentoController();
    $controller->editarForm($id);
    $routeFound = true;
} elseif ($uri === '/usuarios') {
    checkAuth($uri);
    $controller = new UsuarioController();
    $controller->listar();
    $routeFound = true;
} elseif ($uri === '/usuarios/criar' && $method === 'GET') {
    checkAuth($uri);
    $controller = new UsuarioController();
    $controller->criarForm();
    $routeFound = true;
} elseif (preg_match('#^/usuarios/editar/(\d+)$#', $uri, $matches) && $method === 'GET') {
    checkAuth($uri);
    $id = $matches[1];
    $controller = new UsuarioController();
    $controller->editarForm($id);
    $routeFound = true;
}

// Se nenhuma rota for encontrada
if (!$routeFound) {
    header("HTTP/1.0 404 Not Found");
    echo "Página não encontrada";
    echo "<p>URI: " . htmlspecialchars($uri) . "</p>";
    echo "<p>Método: " . htmlspecialchars($method) . "</p>";
}
