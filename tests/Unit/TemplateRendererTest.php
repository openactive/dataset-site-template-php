<?php

namespace OpenActive\DatasetSiteTemplate\Tests;

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
    public function testTemplateRendererInstance($renderer, $json, $data)
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
    public function testRenderString($renderer, $json, $data)
    {
        $this->assertInternalType(
            "string",
            $renderer->render($data)
        );
    }

    /**
     * @return array
     */
    public function templateRendererProvider()
    {
        $json = file_get_contents(__DIR__."/../../example.json");

        return array(
            array(new TemplateRenderer(), $json, json_decode($json, true))
        );
    }
}
