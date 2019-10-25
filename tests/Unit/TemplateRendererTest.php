<?php

namespace OpenActive\DatasetSiteTemplate\Tests;

use OpenActive\DatasetSiteTemplate\DistributionType;
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
    public function testTemplateRendererInstance($renderer, $data)
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
    public function testRenderString($renderer, $data)
    {
        $this->assertInternalType(
            "string",
            $renderer->renderSimpleDatasetSite($data)
        );
    }

    /**
     * @return array
     */
    public function templateRendererProvider()
    {
        $data = array(
            "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
            // TODO: Where does bookingBaseUrl go?
            "bookingBaseUrl" => "https://ourparks.org.uk/openbooking/",
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

        return array(
            array(new TemplateRenderer(), $data)
        );
    }
}
