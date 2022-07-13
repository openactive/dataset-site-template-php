<?php

namespace OpenActive\DatasetSiteTemplate\Tests;

use OpenActive\DatasetSiteTemplate\FeedType;
use OpenActive\DatasetSiteTemplate\TemplateRenderer;
use PHPUnit\Framework\TestCase;

/**
 * TemplateRenderer specific tests.
 */
class TemplateRendererTest extends TestCase
{
    /**
     * Test that the template renderer is an instance of itself
     * (i.e. the constructor worked).
     *
     * @dataProvider templateRendererProvider
     * @return void
     */
    public function testTemplateRendererInstance($renderer, $data, $supportedFeedTypes)
    {
        $this->assertInstanceOf(
            "\\OpenActive\\DatasetSiteTemplate\\TemplateRenderer",
            $renderer
        );
    }

    /**
     * Test that the template renderer renders a string for the single-file template.
     *
     * @dataProvider templateRendererProvider
     * @return void
     */
    public function testRenderString($renderer, $data, $supportedFeedTypes)
    {
        // assertInternalType has been deprecated by phpunit
        $outputHtml = $renderer->renderSimpleDatasetSite($data, $supportedFeedTypes);
        if (method_exists($this,'assertIsString')) {
            $this->assertIsString($outputHtml);
        } else {
            $this->assertInternalType(
                "string",
                $outputHtml
            );
        }
    }

    /**
     * Test that the template renderer renders a string for the CSP template.
     *
     * @dataProvider templateRendererProvider
     * @return void
     */
    public function testRenderStringCSP($renderer, $data, $supportedFeedTypes)
    {
        // assertInternalType has been deprecated by phpunit
        $outputHtml = $renderer->renderSimpleDatasetSite($data, $supportedFeedTypes, "/example/asset/path");
        if (method_exists($this,'assertIsString')) {
            $this->assertIsString($outputHtml);
        } else {
            $this->assertInternalType(
                "string",
                $outputHtml
            );
        }
    }

    /**
     * @return array
     */
    public function templateRendererProvider()
    {
        $data = array(
            "datasetDiscussionUrl" => "https://github.com/gll-better/opendata",
            "datasetSiteUrl" => "https://halo-odi.legendonlineservices.co.uk/openactive/",
            "datasetDocumentationUrl" => "https://docs.acmebooker.example.com/",
            "datasetLanguages" => array("en-GB"),
            "organisationEmail" => "info@better.org.uk",
            "organisationLegalEntity" => "GLL",
            "openDataFeedBaseUrl" => "https://customer.example.com/feed/",
            "organisationLogoUrl" => "http://data.better.org.uk/images/logo.png",
            "organisationName" => "Better",
            "organisationUrl" => "https://www.better.org.uk/",
            "organisationPlainTextDescription" => "Established in 1993, GLL is the largest UK-based charitable social enterprise delivering leisure, health and community services. Under the consumer facing brand Better, we operate 258 public Sports and Leisure facilities, 88 libraries, 10 children’s centres and 5 adventure playgrounds in partnership with 50 local councils, public agencies and sporting organisations. Better leisure facilities enjoy 46 million visitors a year and have more than 650,000 members.",
            "platformName" => "AcmeBooker",
            "platformUrl" => "https://acmebooker.example.com/",
            "platformSoftwareVersion" => "2.0",
            "backgroundImageUrl" => "https://data.better.org.uk/images/bg.jpg",
            "dateFirstPublished" => "2019-10-28",
        );

        $supportedFeedTypes = array(
            FeedType::FACILITY_USE,
            FeedType::SCHEDULED_SESSION,
            FeedType::SESSION_SERIES,
            FeedType::SLOT,
        );

        return array(
            array(new TemplateRenderer(), $data, $supportedFeedTypes)
        );
    }
}
