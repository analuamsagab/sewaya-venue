<?php
declare(strict_types=1);

function json_ok(mixed $data = null, string $message = 'OK'): never
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['ok' => true, 'data' => $data, 'message' => $message],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function json_error(string $message, array $errors = [], int $status = 422): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['ok' => false, 'data' => null, 'message' => $message, 'errors' => $errors],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return $_POST;
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : $_POST;
}
