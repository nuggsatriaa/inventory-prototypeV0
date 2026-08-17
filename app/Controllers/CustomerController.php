<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Customer;

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
    }

    public function index()
    {
        $customers = $this->customerModel->getAll();

        View::render('customers/index', [
            'title'     => 'Master Data Customer',
            'customers' => $customers
        ]);
    }

    public function store()
    {
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerCode = trim($_POST['customer_code'] ?? '');

        if (!empty($customerName) && !empty($customerCode)) {
            $this->customerModel->create([
                'customer_name' => $customerName,
                'customer_code' => $customerCode
            ]);

            header('Location: /customers?status=success_add');
            exit;
        }

        header('Location: /customers?status=error_input');
        exit;
    }

    public function update()
    {
        $id           = (int)($_POST['id'] ?? 0);
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerCode = trim($_POST['customer_code'] ?? '');

        if ($id > 0 && !empty($customerName) && !empty($customerCode)) {
            $this->customerModel->update($id, [
                'customer_name' => $customerName,
                'customer_code' => $customerCode
            ]);

            header('Location: /customers?status=success_update');
            exit;
        }

        header('Location: /customers?status=error_input');
        exit;
    }

    public function delete()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $this->customerModel->delete($id);
            header('Location: /customers?status=success_delete');
            exit;
        }

        header('Location: /customers');
        exit;
    }
}
