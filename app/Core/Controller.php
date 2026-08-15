<?php

declare(strict_types=1);

namespace App\Core;

use App\Helpers\Response;

abstract class Controller
{
    protected function view(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    protected function json(bool $success, string $message = '', array $data = [], int $code = 200): void
    {
        Response::json($success, $message, $data, $code);
    }
}
