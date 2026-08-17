<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Controllers\PartController;
use App\Controllers\LaneController;
use App\Controllers\SubcontController;
use App\Controllers\CustomerController;

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

// 5. Register Route Master Grup/Lane (Meng-cover semua variasi URL)
$router->get('/groups-lanes', [LaneController::class, 'index']);
$router->get('/group-lanes', [LaneController::class, 'index']);

// Endpoint Plural (/groups-lanes)
$router->post('/groups-lanes/store', [LaneController::class, 'store']);
$router->post('/groups-lanes/update', [LaneController::class, 'update']);
$router->post('/groups-lanes/delete', [LaneController::class, 'delete']);
$router->post('/groups-lanes/import', [LaneController::class, 'import']);

// Endpoint Singular (/group-lanes)
$router->post('/group-lanes/store', [LaneController::class, 'store']);
$router->post('/group-lanes/update', [LaneController::class, 'update']);
$router->post('/group-lanes/delete', [LaneController::class, 'delete']);
$router->post('/group-lanes/import', [LaneController::class, 'import']);

// Endpoint Short (/lanes)
$router->post('/lanes/store', [LaneController::class, 'store']);
$router->post('/lanes/update', [LaneController::class, 'update']);
$router->post('/lanes/delete', [LaneController::class, 'delete']);
$router->post('/lanes/import', [LaneController::class, 'import']);

// 6. Register Route Master Subcont
$router->get('/subconts', [SubcontController::class, 'index']);
$router->post('/subconts/store', [SubcontController::class, 'store']);
$router->post('/subconts/update', [SubcontController::class, 'update']);
$router->post('/subconts/delete', [SubcontController::class, 'delete']);
$router->post('/subconts/import', [SubcontController::class, 'import']);

// 7. Register Route Customer
$router->get('/customers', [CustomerController::class, 'index']);
$router->post('/customers/store', [CustomerController::class, 'store']);
$router->post('/customers/update', [CustomerController::class, 'update']);
$router->post('/customers/delete', [CustomerController::class, 'delete']);

// WAJIB DI BARIS PALING BAWAH: Jalankan Dispatcher
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
