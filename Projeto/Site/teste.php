<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
echo "1 ";
require_once __DIR__ . '/includes/config.php';
echo "2 ";
require_once __DIR__ . '/includes/auth.php';
echo "3 ";
var_dump(function_exists('h'), function_exists('csrfCampo'), function_exists('csrfValido'), function_exists('utilizadorAutenticado'), isset($conn));