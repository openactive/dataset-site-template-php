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
     * @param array $data
     * @return string Rendered template
     */
    public function renderSimpleDatasetSite($data)
    {
        // Get available distributionTypes
        $distributionTypeConstants = (
            new \ReflectionClass(new FeedType())
        )->getConstants();

        // Create distribution list based on flags
        $distribution = array();
        if(
            array_key_exists("distributionTypes", $data) &&
            is_array($data["distributionTypes"])
        ) {
            foreach ($data["distributionTypes"] as $distributionType) {
                if(array_search($distributionType, $distributionTypeConstants) !== false) {
                    $distribution[] = new DataDownload([
                        "name" => $distributionType,
                        "encodingFormat" => Meta::RPDE_MEDIA_TYPE,
                        "contentUrl" => $data["openDataBaseUrl"] . "feeds/".
                            Str::kebab($distributionType),
                    ]);
                }
            }
        }

        // Create dataset from data
        $dataset = new Dataset([
            "id" => $data["datasetSiteUrl"],
            "url" => $data["datasetSiteUrl"],
            "name" => $data["name"],
            "description" => "Near real-time availability and rich ".
                "descriptions relating to the sessions and facilities ".
                "available from ".$data["organisationName"].", published ".
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
            "discussionUrl" => $data["datasetSiteDiscussionUrl"],
            "documentation" => $data["documentationUrl"],
            "inLanguage" => "en-GB",
            "schemaVersion" => "https://www.openactive.io/modelling-opportunity-data/2.0/",
            "publisher" => new Organization([
                "name" => $data["organisationName"],
                "legalName" => $data["legalEntity"],
                "description" => $data["plainTextDescription"],
                "email" => $data["email"],
                "url" => $data["organisationUrl"],
                "logo" => new ImageObject([
                    "url" => $data["organisationLogoUrl"]
                ])
            ]),
            "bookingService" => new BookingService([
                "name" => $data["bookingServiceName"],
                "url" => $data["bookingServiceUrl"],
                "softwareVersion" => $data["bookingServiceSoftwareVersion"],
            ]),
            "backgroundImage" => new ImageObject([
                "url" => $data["backgroundImageUrl"],
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
     * @param \OpenActive\Models\Dataset $model The OpenActive model.
     * @param array $additionalData Additional data not belonging to the Dataset model.
     * @return string Rendered template
     */
    public function renderDatasetSite($model)
    {
        if($model instanceof OpenActive\Models\SchemaOrg\Dataset) {
            throw new InvalidArgumentException(
                "Invalid argument type. Argument must be an instance of type ".
                "\OpenActive\Models\SchemaOrg\Dataset, ".
                get_class($model)." given."
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

            $value = $model->$getterName();

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
        $data["json"] = Dataset::serialize($model, true);

        // Render compiled template with JSON-LD data
        return $this->mustacheEngine->render($template, $data);
    }
}
