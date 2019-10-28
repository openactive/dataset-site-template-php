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
     * Render the template from a given array of data.
     *
     * @param array $settings
     * @return string Rendered template
     */
    public function renderSimpleDatasetSite($settings)
    {
        // Get available distributionTypes
        $distributionTypeConstants = (
            new \ReflectionClass(new FeedType())
        )->getConstants();

        // Create distribution list based on flags
        $distribution = array();
        if(
            array_key_exists("distributionTypes", $settings) &&
            is_array($settings["distributionTypes"])
        ) {
            foreach ($settings["distributionTypes"] as $distributionType) {
                if(array_search($distributionType, $distributionTypeConstants) !== false) {
                    $distribution[] = new DataDownload([
                        "name" => $distributionType,
                        "encodingFormat" => Meta::RPDE_MEDIA_TYPE,
                        "contentUrl" => $settings["openDataBaseUrl"] . "feeds/".
                            Str::kebab($distributionType),
                    ]);
                }
            }
        }

        // Create dataset from data
        $dataset = new Dataset([
            "id" => $settings["datasetSiteUrl"],
            "url" => $settings["datasetSiteUrl"],
            "name" => $settings["name"],
            "description" => "Near real-time availability and rich ".
                "descriptions relating to the sessions and facilities ".
                "available from ".$settings["organisationName"].", published ".
                "using the OpenActive Modelling Specification 2.0.",
            "keywords" => [
                "Sessions",
                "Facilities",
                "Activities",
                "Sports",
                "Physical Activity",
                "OpenActive"
            ],
            "license" => "https://creativecommons.org/licenses/by/4.0/",
            "discussionUrl" => $settings["datasetSiteDiscussionUrl"],
            "documentation" => $settings["documentationUrl"],
            "inLanguage" => "en-GB",
            "schemaVersion" => "https://www.openactive.io/modelling-opportunity-data/2.0/",
            "publisher" => new Organization([
                "name" => $settings["organisationName"],
                "legalName" => $settings["legalEntity"],
                "description" => $settings["plainTextDescription"],
                "email" => $settings["email"],
                "url" => $settings["organisationUrl"],
                "logo" => new ImageObject([
                    "url" => $settings["organisationLogoUrl"]
                ])
            ]),
            "bookingService" => new BookingService([
                "name" => $settings["bookingServiceName"],
                "url" => $settings["bookingServiceUrl"],
                "softwareVersion" => $settings["bookingServiceSoftwareVersion"],
            ]),
            "backgroundImage" => new ImageObject([
                "url" => $settings["backgroundImageUrl"],
            ]),
            "distribution" => $distribution,
            "datePublished" => new \DateTime("now", new \DateTimeZone("UTC")),
        ]);

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
        if($dataset instanceof \OpenActive\Models\OA\Dataset) {
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
                $value = JsonLd::prepareDataForSerialization($value);
            } else if (is_array($value)) {
                $value = array_map(
                    function($distributionItem) {
                        return JsonLd::prepareDataForSerialization($distributionItem);
                    },
                    $value
                );
            }

            $data[$attributeName] = $value;
        }

        // JSON-LD is the serialized content
        $data["json"] = Dataset::serialize($dataset, true);

        // Render compiled template with JSON-LD data
        return $this->mustacheEngine->render($template, $data);
    }
}
