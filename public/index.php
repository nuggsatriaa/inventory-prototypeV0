<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Controllers\PartController;
use App\Controllers\LaneController;

// 1. Load Environment Configuration
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// 2. Inisialisasi Router
$router = new Router();

// 3. Register Route Dashboard
$router->get('/', function () {
    \App\Core\View::render('dashboard/index', [
        'title' => 'Dashboard Utama - PT Indonesia Stanley Electric'
    ]);
});

// 4. Register Route Master Parts
$router->get('/parts', [PartController::class, 'index']);
$router->post('/parts/store', [PartController::class, 'store']);
$router->post('/parts/update', [PartController::class, 'update']);
$router->post('/parts/delete', [PartController::class, 'delete']);
$router->post('/parts/import', [PartController::class, 'import']);

// 5. Register Route Master Grup/Lane
$router->get('/groups-lanes', [LaneController::class, 'index']);
$router->post('/lanes/store', [LaneController::class, 'store']);
$router->post('/lanes/update', [LaneController::class, 'update']);
$router->post('/lanes/delete', [LaneController::class, 'delete']);

// WAJIB DI BARIS PALING BAWAH: Jalankan Dispatcher
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
