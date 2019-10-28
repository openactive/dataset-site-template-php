<?php

require __DIR__ . "/../vendor/autoload.php";

use OpenActive\DatasetSiteTemplate\FeedType;
use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Get JSON-LD data

// Create new dataset
$data = array(
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
    "bookingServiceName" => "AcmeBooker",
    "bookingServiceSoftwareVersion" => "0.1.0",
    "bookingServiceUrl" => "https://acmebooker.example.com/",
    "datasetSiteDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "distributionTypes" => array(
        FeedType::FACILITY_USE,
        FeedType::SCHEDULED_SESSION,
        FeedType::SESSION_SERIES,
        FeedType::SLOT,
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
);

// Render compiled template with data
echo (new TemplateRenderer())->renderSimpleDatasetSite($data);
