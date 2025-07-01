<?php
class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        try {
            // Obter configurações do arquivo .env
            $host = env('DB_HOST', 'localhost');
            $dbname = env('DB_NAME', 'agendamento_veiculos');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');

            // Log para depuração (apenas em modo debug)
            if (env('APP_DEBUG', false)) {
                error_log("Tentando conectar ao banco de dados: $dbname@$host");
            }

            $this->conn = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            if (env('APP_DEBUG', false)) {
                error_log("Conexão com o banco de dados estabelecida com sucesso");
            }
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
