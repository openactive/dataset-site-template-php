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
     * Test that the template renderer renders a string.
     *
     * @dataProvider templateRendererProvider
     * @return void
     */
    public function testRenderString($renderer, $data, $supportedFeedTypes)
    {
        $this->assertInternalType(
            "string",
            $renderer->renderSimpleDatasetSite($data, $supportedFeedTypes)
        );
    }

    /**
     * @return array
     */
    public function templateRendererProvider()
    {
        $data = array(
            "datasetDiscussionUrl" => "https://github.com/ourparks/opendata",
            "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
            "datasetDocumentationUrl" => "https://ourparks.org.uk/openbooking/",
            "datasetLanguages" => array("en-GB"),
            "organisationEmail" => "hello@ourparks.org.uk",
            "organisationLegalEntity" => "Our Parks",
            "openDataFeedBaseUrl" => "https://ourparks.org.uk/opendata/",
            "organisationLogoUrl" => "https://ourparks.org.uk/logo.png",
            "organisationName" => "Our Parks",
            "organisationUrl" => "https://ourparks.org.uk/",
            "organisationPlainTextDescription" => "Our Parks - turn up tone up!",
            "platformName" => "AcmeBooker",
            "platformUrl" => "https://acmebooker.example.com/",
            "platformSoftwareVersion" => "0.1.0",
            "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
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
