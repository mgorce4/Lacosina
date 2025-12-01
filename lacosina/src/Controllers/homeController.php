<?php

namespace App\Controllers;

class homeController{
    
    //fonction permettant d'afficher la page d'accueil
    public function index(){
        require_once(__DIR__ . '/../Views/home.php');
    }
}