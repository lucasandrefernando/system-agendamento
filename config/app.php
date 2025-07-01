<?php
// URL base do sistema
define('BASE_URL', baseUrl());

// Função para gerar URLs
function url($path = '')
{
    return baseUrl($path);
}
