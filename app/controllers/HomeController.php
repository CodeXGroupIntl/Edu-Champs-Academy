<?php 

    class HomeController
    {
        public function index()
        {
            $pageTitle = "Home | Edu-Champs Academy";
            require_once __DIR__ . '/../views/home.php';
        }
    }

?>