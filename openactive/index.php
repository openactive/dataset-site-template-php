<?php

require __DIR__ . "/../vendor/autoload.php";

use OpenActive\DatasetSiteTemplate\FeedType;
use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Get JSON-LD data

// Create new dataset
$settings = array(
    "datasetDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "datasetDocumentationUrl" => "https://ourparks.org.uk/openbooking/",
    "organisationEmail" => "hello@ourparks.org.uk",
    "organisationLegalEntity" => "Our Parks",
    "name" => "Our Parks Sessions",
    "openDataFeedBaseUrl" => "https://ourparks.org.uk/opendata/",
    "organisationLogoUrl" => "https://ourparks.org.uk/logo.png",
    "organisationName" => "Our Parks",
    "organisationUrl" => "https://ourparks.org.uk/",
    "organisationPlainTextDescription" => "Our Parks - turn up tone up!",
    "platformName" => "AcmeBooker",
    "platformSoftwareVersion" => "0.1.0",
    "platformUrl" => "https://acmebooker.example.com/",
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
);

$feedTypes = array(
    FeedType::FACILITY_USE,
    FeedType::SCHEDULED_SESSION,
    FeedType::SESSION_SERIES,
    FeedType::SLOT,
);

// Render compiled template with data
echo (new TemplateRenderer())->renderSimpleDatasetSite($settings, $feedTypes);
