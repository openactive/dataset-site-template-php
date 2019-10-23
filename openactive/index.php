<?php

require "../vendor/autoload.php";

use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Get JSON-LD data
$json = file_get_contents("../example.json");
$data = json_decode($json, true);

// Render compiled template with JSON-LD data
echo (new TemplateRenderer())->render($data);
