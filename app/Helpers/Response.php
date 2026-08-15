<?php

declare(strict_types=1);

namespace App\Helpers;

class Response
{
    public static function json(bool $success, string $message = '', array $data = [], int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }
}
