<?php

require __DIR__ . "/../vendor/autoload.php";

use OpenActive\DatasetSiteTemplate\DistributionType;
use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Get JSON-LD data

// Create new dataset
$data = array(
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
    "datasetSiteDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "distributionTypes" => array(
        DistributionType::FACILITY_USE,
        DistributionType::SCHEDULED_SESSION,
        DistributionType::SESSION_SERIES,
        DistributionType::SLOT,
    ),
    "documentationUrl" => "https://ourparks.org.uk/openbooking/",
    "email" => "hello@ourparks.org.uk",
    "legalEntity" => "Our Parks",
    "name" => "Our Parks Sessions",
    "openDataBaseUrl" => "https://ourparks.org.uk/opendata/",
    "organisationLogoUrl" => "https://ourparks.org.uk/logo.png",
    "organisationName" => "Our Parks",
    "organisationUrl" => "https://ourparks.org.uk/",
    "plainTextDescription" => "Our Parks - turn up tone up!",
    "platformName" => "AcmeBooker",
    "platformUrl" => "https://acmebooker.example.com/",
    "softwareVersion" => "0.1.0",
);

// Render compiled template with data
echo (new TemplateRenderer())->renderSimpleDatasetSite($data);
