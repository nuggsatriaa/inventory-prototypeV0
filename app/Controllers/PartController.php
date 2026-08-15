<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Part;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PartController extends Controller
{
    private Part $partModel;

    public function __construct()
    {
        $this->partModel = new Part();
    }

    public function index(): void
    {
        $parts = $this->partModel->getAll();
        $this->view('parts/index', [
            'title' => 'Master Parts (ICS) - PT Stanley Electric',
            'parts' => $parts,
        ]);
    }

    public function store(): void
    {
        $this->partModel->create($_POST);
        header('Location: /parts');
        exit;
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $this->partModel->update($id, $_POST);
        header('Location: /parts');
        exit;
    }

    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $this->partModel->delete($id);
        header('Location: /parts');
        exit;
    }
    public function import(): void
    {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /parts?error=File tidak valid');
            exit;
        }

        $filePath = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Baris 1 adalah Header, data mulai dari baris 2
            for ($i = 2; $i <= count($sheetData); $i++) {
                $row = $sheetData[$i];

                $partNumber = trim((string)($row['A'] ?? ''));
                $partName   = trim((string)($row['B'] ?? ''));
                $type       = trim((string)($row['C'] ?? ''));
                $groupCode  = trim((string)($row['D'] ?? ''));
                $minStock   = (int)($row['E'] ?? 0);

                if (!empty($partNumber) && !empty($partName)) {
                    $this->partModel->create([
                        'part_number' => $partNumber,
                        'part_name'   => $partName,
                        'type'        => $type,
                        'group_code'  => $groupCode,
                        'min_stock'   => $minStock,
                    ]);
                }
            }

            header('Location: /parts?success=Import data berhasil');
        } catch (\Throwable $e) {
            header('Location: /parts?error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}
