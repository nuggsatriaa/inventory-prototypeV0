<?php

namespace App\Controllers;

use App\Core\View;

class LaneController
{
    public function index()
    {
        // Data Dummy untuk Pengetesan Tampilan
        $lanes = [
            [
                'id' => 1,
                'group_name' => 'PP1',
                'category_name' => 'Injection',
                'lane_name' => 'PM62'
            ],
            [
                'id' => 2,
                'group_name' => 'OutPlant',
                'category_name' => 'SubCont',
                'lane_name' => 'HASURA'
            ],
            [
                'id' => 3,
                'group_name' => 'LA1',
                'category_name' => 'Assy 2W',
                'lane_name' => 'Line 01'
            ]
        ];

        View::render('lanes/index', [
            'title' => 'Master Data Group & Lane',
            'lanes' => $lanes
        ]);
    }

    public function store()
    {
        $groupName    = $_POST['group_name'] ?? '';
        $categoryName = $_POST['category_name'] ?? '';
        $laneName     = $_POST['lane_name'] ?? '';

        // TODO: Query Insert DB

        header('Location: /groups-lanes');
        exit;
    }

    public function update()
    {
        $id           = $_POST['id'] ?? null;
        $groupName    = $_POST['group_name'] ?? '';
        $categoryName = $_POST['category_name'] ?? '';
        $laneName     = $_POST['lane_name'] ?? '';

        // TODO: Query Update DB

        header('Location: /groups-lanes');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;

        // TODO: Query Delete DB

        header('Location: /groups-lanes');
        exit;
    }
}
