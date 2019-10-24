<?php

require __DIR__ . "/../vendor/autoload.php";

use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Get JSON-LD data

// Create new dataset
$data = array(
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
    // TODO: Where does bookingBaseUrl go?
    "bookingBaseUrl" => "https://ourparks.org.uk/openbooking/",
    "datasetSiteDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "email" => "hello@ourparks.org.uk",
    "includeCourseInstanceFeed" => false,
    "includeEventFeed" => false,
    "includeScheduledSessionFeed" => true,
    "includeSessionSeriesFeed" => true,
    "legalEntity" => "Our Parks",
    "name" => "Our Parks Sessions",
    "openDataBaseUrl" => "https://ourparks.org.uk/opendata/",
    "organisationLogoUrl" => "https://ourparks.org.uk/logo.png",
    "organisationName" => "Our Parks",
    "organisationUrl" => "https://ourparks.org.uk/",
    "plainTextDescription" => "Our Parks - turn up tone up!",
    // TODO: should documentationUrl, platformName, and platformUrl be a parameter?
    "documentationUrl" => "https://ourparks.org.uk/openbooking/",
    "platformName" => "AcmeBooker",
    "platformUrl" => "https://acmebooker.example.com/",
    "softwareVersion" => "0.1.0",
);

// Render compiled template with data
echo (new TemplateRenderer())->renderSimpleDatasetSite($data);
