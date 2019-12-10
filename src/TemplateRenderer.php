<?php

namespace OpenActive\DatasetSiteTemplate;

use Mustache_Engine;
use OpenActive\DatasetSiteTemplate\Meta;
use OpenActive\Exceptions\InvalidArgumentException;
use OpenActive\Helpers\JsonLd;
use OpenActive\Helpers\Str;
use OpenActive\Models\OA\BookingService;
use OpenActive\Models\OA\DataDownload;
use OpenActive\Models\OA\Dataset;
use OpenActive\Models\OA\ImageObject;
use OpenActive\Models\OA\Organization;

/**
 *
 */
class TemplateRenderer
{
    /**
     * The mustache engine implementation.
     *
     * @var \Mustache_Engine
     */
    protected $mustacheEngine;

    /**
     * Create a new template renderer instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mustacheEngine = new Mustache_Engine();
    }

    /**
     * Render the template from a given array of settings and supported feed types.
     *
     * @param array $settings
     * @param FeedType[] $supportedFeedTypes
     * @return string Rendered template
     */
    public function renderSimpleDatasetSite($settings, $supportedFeedTypes)
    {
        // Get available distributionTypes
        $distributionTypeConstants = (
            new \ReflectionClass(new FeedType())
        )->getConstants();

        // Get all feed configurations
        $allFeedConfigurations = FeedConfiguration::allFeedConfigurations();
        // Selected feed types are the intersection of keys
        // of the provided feed types and all the feed configurations
        $selectedFeedConfigurations = array_values(array_intersect_key(
            $allFeedConfigurations,
            array_flip($supportedFeedTypes)
        ));
        // Create DataDownload list
        $dataDownloads = array_map(
            function($feedConfiguration) use ($settings) {
                return new DataDownload([
                    "name" => $feedConfiguration->getName(),
                    "additionalType" => $feedConfiguration->getSameAs(),
                    "encodingFormat" => Meta::RPDE_MEDIA_TYPE,
                    "contentUrl" => $settings["openDataFeedBaseUrl"] . $feedConfiguration->getDefaultFeedPath()
                ]);
            },
            $selectedFeedConfigurations
        );

        // Build data feed descriptions from displayName of $selectedFeedConfigurations
        $dataFeedDescriptions = array_map(
            function($feedConfiguration) {
                return $feedConfiguration->getDisplayName();
            },
            $selectedFeedConfigurations
        );
        // Remove empty elements
        $dataFeedDescriptions = array_filter($dataFeedDescriptions);
        // Remove duplicate elements
        $dataFeedDescriptions = array_unique($dataFeedDescriptions);
        // Re-index array 0 - length
        $dataFeedDescriptions = array_values($dataFeedDescriptions);

        return $this->renderSimpleDatasetSiteFromDataDownloads(
            $settings,
            $dataDownloads,
            $dataFeedDescriptions
        );
    }

    /**
     * Render the template from a given array of settings, data downloads,
     * and data feed descriptions.
     *
     * @param array $settings
     * @param DataDownload[] $dataDownloads
     * @param string[] $dataFeedDescriptions
     * @return string Rendered template
     */
    public function renderSimpleDatasetSiteFromDataDownloads(
        $settings,
        $dataDownloads,
        $dataFeedDescriptions
    )
    {
        // Pre-process list of feed descriptions
        $dataFeedHumanisedList = $this->toHumanisedList($dataFeedDescriptions);
        $keywords = array_merge($dataFeedDescriptions, array(
            "Activities",
            "Sports",
            "Physical Activity",
            "OpenActive"
        ));

        // Create dataset from data
        $dataset = new Dataset([
            "id" => $settings["datasetSiteUrl"],
            "url" => $settings["datasetSiteUrl"],
            "name" => $settings["organisationName"] . " " . $dataFeedHumanisedList,
            "description" => "Near real-time availability and rich ".
                "descriptions relating to the ".
                strtolower($dataFeedHumanisedList)." available from ".
                $settings["organisationName"],
            "keywords" => $keywords,
            "license" => "https://creativecommons.org/licenses/by/4.0/",
            "discussionUrl" => $settings["datasetDiscussionUrl"],
            "documentation" => $settings["datasetDocumentationUrl"],
            "inLanguage" => $settings["datasetLanguages"],
            "schemaVersion" => "https://www.openactive.io/modelling-opportunity-data/2.0/",
            "publisher" => new Organization([
                "name" => $settings["organisationName"],
                "legalName" => $settings["organisationLegalEntity"],
                "description" => $settings["organisationPlainTextDescription"],
                "email" => $settings["organisationEmail"],
                "url" => $settings["organisationUrl"],
                "logo" => new ImageObject([
                    "url" => $settings["organisationLogoUrl"]
                ])
            ]),
            "dateModified" => new \DateTime("now", new \DateTimeZone("UTC")),
            "backgroundImage" => new ImageObject([
                "url" => $settings["backgroundImageUrl"],
            ]),
            "distribution" => $dataDownloads,
            "datePublished" => $settings["dateFirstPublished"],
        ]);

        // Only set BookingService if valid platformName provided
        if(
            array_key_exists("platformName", $settings) &&
            is_string($settings["platformName"]) &&
            strlen($settings["platformName"]) > 0
        ) {
            $dataset->setBookingService(new BookingService([
                "name" => $settings["platformName"],
                "url" => $settings["platformUrl"],
                "softwareVersion" => $settings["platformSoftwareVersion"],
            ]));
        }

        // Render compiled template with JSON-LD data
        return $this->renderDatasetSite($dataset);
    }

    /**
     * Render the template from a given OpenActive dataset model.
     *
     * @param \OpenActive\Models\Dataset $dataset The OpenActive model.
     * @return string Rendered template
     */
    public function renderDatasetSite($dataset)
    {
        if($dataset instanceof OpenActive\Models\OA\Dataset) {
            throw new InvalidArgumentException(
                "Invalid argument type. Argument must be an instance of type ".
                "\OpenActive\Models\OA\Dataset, ".
                get_class($dataset)." given."
            );
        }

        // Get template from FS
        $template = file_get_contents(__DIR__."/datasetsite.mustache");

        // Build data from model's getters
        $data = array();
        $attributeNames = array(
            "backgroundImage",
            "bookingService",
            "dateModified",
            "datePublished",
            "description",
            "discussionUrl",
            "distribution",
            "documentation",
            "license",
            "name",
            "publisher",
            "schemaVersion",
            "url",
        );
        foreach ($attributeNames as $attributeName) {
            $getterName = "get" . Str::pascal($attributeName);

            $value = $dataset->$getterName();

            // If an object, we prepare it for serialization
            if(is_object($value)) {
                $value = JsonLd::prepareDataForSerialization($value, null, true);
            } else if (is_array($value)) {
                $value = array_map(
                    static function($distributionItem) {
                        return JsonLd::prepareDataForSerialization($distributionItem, null, true);
                    },
                    $value
                );
            }

            $data[$attributeName] = $value;
        }

        // JSON-LD is the serialized content
        $data["json"] = Dataset::serialize($dataset, true, true);

        // Render compiled template with JSON-LD data
        return $this->mustacheEngine->render($template, $data);
    }

    /**
     * Converts a list of nouns into a human readable list.
     * ["One", "Two", "Three", "Four"] => "One, Two, Three, and Four"
     *
     * @param string[] $list List of nouns
     * @return string containing human readable list
     */
    private function toHumanisedList($list)
    {
        // Length of list
        $listLength = count($list);

        if($listLength === 0) {
            return "";
        }

        if($listLength === 1) {
            return $list[0];
        }

        // Prepend last item with " and "
        $listLastElement = " and ".$list[$listLength - 1];

        return implode(", ", array_slice($list, 0, -1)) . $listLastElement;
    }
}
