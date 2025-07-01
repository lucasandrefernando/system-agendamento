<?php

/**
 * Carregador de variáveis de ambiente
 * 
 * Este arquivo carrega as variáveis de ambiente do arquivo .env
 */

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception("O arquivo .env não foi encontrado em: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentários
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Processar variáveis de ambiente
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remover aspas se existirem
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }

        // Definir a variável de ambiente
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Carregar variáveis de ambiente
$envPath = __DIR__ . '/.env';
try {
    loadEnv($envPath);
    // Definir timezone
    date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'America/Sao_Paulo');
} catch (Exception $e) {
    die("Erro ao carregar variáveis de ambiente: " . $e->getMessage());
}

/**
 * Função para obter variáveis de ambiente
 */
function env($key, $default = null)
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    // Converter valores especiais
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
        case 'empty':
        case '(empty)':
            return '';
    }

    return $value;
}

/**
 * Função para obter a URL base
 */
function baseUrl($path = '')
{
    $baseUrl = env('APP_URL', 'http://localhost');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}
