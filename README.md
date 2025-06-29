# Sistema de Agendamento de Veículos

Um sistema completo para gerenciamento de agendamentos de veículos, desenvolvido em PHP com padrão MVC.

## Funcionalidades

- Dashboard com indicadores e gráficos
- Gerenciamento de agendamentos (criar, editar, excluir)
- Visualização de horários disponíveis
- Calendário interativo
- Controle de usuários e permissões
- Relatórios e estatísticas

## Tecnologias Utilizadas

- PHP 7.4+
- MySQL
- Bootstrap 5
- Chart.js
- FullCalendar
- SweetAlert2
- Flatpickr

## Instalação

1. Clone o repositório
2. Configure o banco de dados em `config/database.php`
3. Importe o arquivo SQL `database.sql` para criar as tabelas
4. Acesse o sistema através do navegador

## Configuração

Copie o arquivo `config/database.example.php` para `config/database.php` e configure as credenciais do banco de dados:

````php
<?php
define('DB_HOST', 'seu_host');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'nome_do_banco');
Licença
Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes.


3. **Crie um arquivo `database.example.php`** em `config/`:

```php
<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = 'localhost';
        $user = 'seu_usuario';
        $pass = 'sua_senha';
        $dbname = 'nome_do_banco';

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("SET NAMES utf8");
        } catch(PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
````
