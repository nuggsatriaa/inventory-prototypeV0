<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Lane;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LaneController
{
    private $laneModel;

    public function __construct()
    {
        $this->laneModel = new Lane();
    }

    public function index()
    {
        // 1. Ambil data dari database via Model
        $lanes = $this->laneModel->getAll();

        // 2. Kirim data ke view
        View::render('lanes/index', [
            'title' => 'Master Data Group & Lane',
            'lanes' => $lanes
        ]);
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                header('Location: /groups-lanes?status=error_upload');
                exit;
            }

            try {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                // Loop data Excel (Skip baris header index 0)
                foreach ($sheetData as $index => $row) {
                    if ($index === 0) continue;

                    $groupName    = trim((string)($row[0] ?? ''));
                    $categoryName = trim((string)($row[1] ?? ''));
                    $laneName     = trim((string)($row[2] ?? ''));

                    // Abaikan baris jika data utama kosong
                    if (empty($groupName) && empty($laneName)) {
                        continue;
                    }

                    // Simpan data via Lane Model
                    $this->laneModel->create([
                        'group_name'    => $groupName,
                        'category_name' => $categoryName,
                        'lane_name'     => $laneName
                    ]);
                }

                header('Location: /groups-lanes?status=success');
                exit;
            } catch (\Exception $e) {
                die("Gagal Import: " . $e->getMessage());
            }
        }
        // Saat sukses
        header('Location: /groups-lanes?status=success');
        exit;

        // Saat gagal upload
        header('Location: /groups-lanes?status=error_upload');
        exit;
    }

    public function store()
    {
        $groupName    = $_POST['group_name'] ?? '';
        $categoryName = $_POST['category_name'] ?? '';
        $laneName     = $_POST['lane_name'] ?? '';

        // Simpan data ke database jika input tidak kosong
        if (!empty($groupName) && !empty($categoryName) && !empty($laneName)) {
            $this->laneModel->create([
                'group_name'    => $groupName,
                'category_name' => $categoryName,
                'lane_name'     => $laneName
            ]);
        }

        header('Location: /groups-lanes');
        exit;
    }

    public function update()
    {
        $id           = (int)($_POST['id'] ?? 0);
        $groupName    = $_POST['group_name'] ?? '';
        $categoryName = $_POST['category_name'] ?? '';
        $laneName     = $_POST['lane_name'] ?? '';

        if ($id > 0 && !empty($groupName) && !empty($categoryName) && !empty($laneName)) {
            $this->laneModel->update($id, [
                'group_name'    => $groupName,
                'category_name' => $categoryName,
                'lane_name'     => $laneName
            ]);
        }

        header('Location: /groups-lanes');
        exit;
    }

    public function delete()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->laneModel->delete($id);
        }

        header('Location: /groups-lanes');
        exit;
    }
}
