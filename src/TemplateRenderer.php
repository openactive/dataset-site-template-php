<?php

namespace OpenActive\DatasetSiteTemplate;

use Mustache_Engine;

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
     * Render the template from a given associative array representation
     * of the JSON-LD.
     *
     * @param array $data The JSON-LD associative array representation.
     * @return string Rendered template
     */
    public function render($data)
    {
        // Get template from FS
        $template = file_get_contents(__DIR__."/datasetsite.mustache");

        // Render compiled template with JSON-LD data
        return $this->mustacheEngine->render($template, $data);
    }
}
