<?php

namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = [])
    {
        // 1. Extrak data array menjadi variabel lokal ($lanes, $title, dsb)
        extract($data);

        // 2. Mulai Output Buffering untuk menangkap HTML dari file view
        ob_start();
        $viewFile = __DIR__ . "/../../views/" . $viewPath . ".php";

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View file [{$viewFile}] tidak ditemukan.");
        }

        // 3. Simpan seluruh isi HTML view ke variabel $content
        $content = ob_get_clean();

        // 4. Panggil layout utama (yang berisi header, sidebar, dan echo $content)
        require_once __DIR__ . "/../../views/layouts/main.php";
    }
}
