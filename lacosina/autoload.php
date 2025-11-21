<?php

/**
 * Autoloader PSR-4 pour le projet Lacosina
 * Fichier situé dans lacosina/ pour être accessible depuis Docker
 */

spl_autoload_register(function ($class) {
    // Préfixe du namespace de base
    $prefix = 'App\\';
    
    // Répertoire de base pour le namespace (depuis lacosina/)
    $base_dir = __DIR__ . '/src/';
    
    // Vérifier si la classe utilise le namespace de base
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Non, passer à l'autoloader suivant
        return;
    }
    
    // Obtenir le nom de classe relatif
    $relative_class = substr($class, $len);
    
    // Remplacer le namespace par le chemin du fichier
    // Remplacer les backslashes par des slashes et ajouter .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Si le fichier existe, le charger
    if (file_exists($file)) {
        require $file;
    }
});
