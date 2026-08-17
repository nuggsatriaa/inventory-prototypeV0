<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Part;
use App\Models\Customer;
use App\Models\Lane;
use App\Models\Subcont;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PartController
{
    private Part $partModel;
    private Customer $customerModel;
    private Lane $laneModel;
    private Subcont $subcontModel;

    public function __construct()
    {
        $this->partModel     = new Part();
        $this->customerModel = new Customer();
        $this->laneModel     = new Lane();
        $this->subcontModel  = new Subcont();
    }

    public function index(): void
    {
        $categories = ['Injection', 'Surface Treatment', 'Assy 2W', 'Assy 4W', 'SMT', 'SUBCONT'];

        View::render('parts/index', [
            'title'      => 'Master Data Parts',
            'parts'      => $this->partModel->getAll(),
            'customers'  => $this->customerModel->getAll(),
            'categories' => $categories,
            'lanes'      => $this->laneModel->getAllGroupedByCategory(),
            'subconts'   => $this->subcontModel->getAll()
        ]);
    }

    public function import(): void
    {
        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $worksheet   = $spreadsheet->getActiveSheet();
                $rows        = $worksheet->toArray(null, true, true, true);

                // Load semua data customer ke memory untuk pencarian cepat
                $allCustomers = $this->customerModel->getAll();
                $customerMap  = [];

                foreach ($allCustomers as $cust) {
                    $id = $cust['id'];

                    // Mapping berdasarkan customer_code (contoh: AHM, YIMM, KMI)
                    if (!empty($cust['customer_code'])) {
                        $codeKey = strtoupper(trim((string)$cust['customer_code']));
                        $customerMap[$codeKey] = $id;
                    }

                    // Mapping berdasarkan customer_name (backup jika di excel pakai nama lengkap)
                    if (!empty($cust['customer_name'])) {
                        $nameKey = strtoupper(trim((string)$cust['customer_name']));
                        $customerMap[$nameKey] = $id;
                    }
                }

                $isHeader = true;

                foreach ($rows as $row) {
                    if ($isHeader) {
                        $isHeader = false;
                        continue; // Skip baris header
                    }

                    $rawIcs       = isset($row['A']) ? trim((string)$row['A']) : '';
                    $partName     = isset($row['B']) ? trim((string)$row['B']) : '';
                    $customerInput = isset($row['F']) ? strtoupper(trim((string)$row['F'])) : '';

                    // Filter ics_no (ambil 10 digit angka)
                    $cleanIcs = substr(preg_replace('/[^0-9]/', '', $rawIcs), 0, 10);

                    // Skip jika ics_no atau part_name kosong
                    if (empty($cleanIcs) || empty($partName)) {
                        continue;
                    }

                    // Match nilai "AHM" / "YIMM" / "KMI" dari Excel ke Customer ID
                    $customerId = $customerMap[$customerInput] ?? null;

                    $importData = [
                        'ics_no'      => $cleanIcs,                                  // Kolom A
                        'part_name'   => $partName,                                  // Kolom B
                        'type'        => !empty($row['C']) ? trim((string)$row['C']) : null, // Kolom C
                        'category'    => !empty($row['D']) ? trim((string)$row['D']) : null, // Kolom D
                        'no_lane'     => !empty($row['E']) ? trim((string)$row['E']) : null, // Kolom E
                        'customer_id' => $customerId,                                // Kolom F -> Matched Customer ID
                        'min_stock'   => isset($row['G']) ? (int)$row['G'] : 0,              // Kolom G
                        'source_id'   => null
                    ];

                    $this->partModel->create($importData);
                }

                header("Location: /parts?status=success");
                exit;
            } catch (\Exception $e) {
                header("Location: /parts?status=error_upload");
                exit;
            }
        }

        header("Location: /parts?status=error_upload");
        exit;
    }

    public function store(): void
    {
        $rawIcs = $_POST['ics_no'] ?? '';
        $icsNo = substr(preg_replace('/[^0-9]/', '', $rawIcs), 0, 10);

        $data = [
            'ics_no'      => !empty($icsNo) ? $icsNo : null,
            'part_name'   => $_POST['part_name'] ?? null,
            'type'        => $_POST['type'] ?? null,
            'category'    => $_POST['category'] ?? null,
            'source_id'   => $_POST['source_id'] ?? $_POST['lane_id'] ?? null,
            'no_lane'     => $_POST['no_lane'] ?? null,
            'customer_id' => $_POST['customer_id'] ?? null,
            'min_stock'   => (int)($_POST['min_stock'] ?? 0),
        ];

        if (!empty($data['ics_no']) && !empty($data['part_name'])) {
            $this->partModel->create($data);
            header('Location: /parts?status=success_add');
            exit;
        }

        header('Location: /parts?status=error_input');
        exit;
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $rawIcs = $_POST['ics_no'] ?? '';
        $icsNo = substr(preg_replace('/[^0-9]/', '', $rawIcs), 0, 10);

        $data = [
            'ics_no'      => !empty($icsNo) ? $icsNo : null,
            'part_name'   => $_POST['part_name'] ?? null,
            'type'        => $_POST['type'] ?? null,
            'category'    => $_POST['category'] ?? null,
            'source_id'   => $_POST['source_id'] ?? null,
            'customer_id' => $_POST['customer_id'] ?? null,
            'min_stock'   => (int)($_POST['min_stock'] ?? 0),
        ];

        if ($id > 0 && !empty($data['part_name'])) {
            $this->partModel->update($id, $data);
            header('Location: /parts?status=success_update');
            exit;
        }

        header('Location: /parts?status=error_input');
        exit;
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->partModel->delete($id);
            header('Location: /parts?status=success_delete');
            exit;
        }

        header('Location: /parts');
        exit;
    }
}
