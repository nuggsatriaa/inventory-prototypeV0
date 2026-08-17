<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Subcont;

class SubcontController
{
    private $subcontModel;

    public function __construct()
    {
        $this->subcontModel = new Subcont();
    }

    public function index()
    {
        $subconts = $this->subcontModel->getAll();

        View::render('subconts/index', [
            'title'    => 'Master Data SubCont',
            'subconts' => $subconts
        ]);
    }

    public function store()
    {
        $subcontName = $_POST['subcont_name'] ?? '';
        $category    = $_POST['category'] ?? '';

        if (!empty($subcontName) && !empty($category)) {
            $this->subcontModel->create([
                'subcont_name' => $subcontName,
                'category'     => $category
            ]);
        }

        header('Location: /subconts');
        exit;
    }

    public function update()
    {
        $id          = (int)($_POST['id'] ?? 0);
        $subcontName = $_POST['subcont_name'] ?? '';
        $category    = $_POST['category'] ?? '';

        if ($id > 0 && !empty($subcontName) && !empty($category)) {
            $this->subcontModel->update($id, [
                'subcont_name' => $subcontName,
                'category'     => $category
            ]);
        }

        header('Location: /subconts');
        exit;
    }

    public function delete()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->subcontModel->delete($id);
        }

        header('Location: /subconts');
        exit;
    }
}
