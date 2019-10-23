<?php

require "../vendor/autoload.php";

$mustacheEngine = new Mustache_Engine;

// Get template from FS
$template = file_get_contents("../datasetsite.mustache");

// Get JSON-LD data
$data = array("planet" => "World!");

// Render compiled template with JSON-LD data
echo $mustacheEngine->render($template, $data);
