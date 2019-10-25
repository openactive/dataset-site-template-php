# dataset-site-template-php
PHP Classes and resources supporting dataset site creation

Tools intended to simplify creation of dataset sites using templates.

For comparison, see the [.NET](https://github.com/openactive/dataset-site-template-example-dotnet) and [Ruby](https://github.com/openactive/dataset-site-template-ruby) implementations.

## Table Of Contents
- [Requirements](#requirements)
- [Usage](#usage)
    - [API](#api)
        - [`renderSimpleDatasetSite($data)`](#rendersimpledatasetsitedata)
        - [`renderDatasetSite($data, $additionalData)`](#renderdatasetsitedataset-additionaldata)
        - [`DistributionType`](#distribution-type)
- [Development](#development)
    - [Installation](#installation)
    - [Example](#example)
    - [Running Tests](#running-tests)

## Requirements
This project requires PHP >=5.6.
While most of the functionality should work down to PHP 5.4, some functionality (especially around parsing of offset for DateTimeZone) will not work with that version of PHP (see the [DateTimeZone PHP docs](https://www.php.net/manual/en/datetimezone.construct.php#refsect1-datetimezone.construct-changelog) for more info).

[Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) is also required for dependency management.

This project also makes use of [Mustache](https://github.com/bobthecow/mustache.php) for rendering the template (installed via Composer).

**Temporary:** You will also need a local copy of the [`models-php`](https://github.com/openactive/models-php) repo.

This repository and the `models-php` will need to co-exist in the same parent directory, for example:
```
projects
|- models-php
|--- src
|--- ...
|- dataset-site-template-php
|--- src
|--- ...
```

## Usage

**Please Note:** This instruction are temporary and based on the current development status.

If you are developing this package, go to the [Development](#development) section.

To install from terminal, run:
```bash
composer require openactive/dataset-site-template-php
```

Wherever you want to render your Dataset page, include the following instructions:
```php
use OpenActive\DatasetSiteTemplate\TemplateRenderer;

// Render compiled template with data
echo (new TemplateRenderer())->renderSimpleDatasetSite($data);
```

Where `$data` could be defined like the following (as an example):
```php
$data = array(
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
    // TODO: Where does bookingBaseUrl go?
    "bookingBaseUrl" => "https://ourparks.org.uk/openbooking/",
    "datasetSiteDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "documentationUrl" => "https://ourparks.org.uk/openbooking/",
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
    "platformName" => "AcmeBooker",
    "platformUrl" => "https://acmebooker.example.com/",
    "softwareVersion" => "1.2.3",
);
```

### API

#### `renderSimpleDatasetSite($data)`

Returns a string corresponding to the compiled HTML, based on the `datasetsite.mustache`, and the provided `$data`.

`$data` must contain the following keys:
- **backgroundImageUrl** - The background image to show on the page;
- **bookingBaseUrl** - TBC. TODO: Where does `bookingBaseUrl` go?
- **datasetSiteDiscussionUrl** - The discussion URL for the dataset
- **datasetSiteUrl** - The dataset site URL;
- **distributionTypes** - An array of distribution model types. Please see the available types [here](#distribution-type)
- **documentationUrl** - The documentation's URL;
- **email** - The email of the publisher of this dataset;
- **legalEntity** - The legal name of the publisher of this dataset;
- **name** - The name of the publisher of this dataset;
- **openDataBaseUrl** - The base OpenData URL for this dataset. This URL is used as a base URL for the feeds;
- **organisationLogoUrl** - A valid image URL of the organisation's logo;
- **organisationName** - The organisation's name;
- **organisationUrl** - The organisation's URL;
- **plainTextDescription** - The publisher's description in plain text;
- **platformName** - The platform's name;
- **platformUrl** - The platform's URL;
- **softwareVersion** - The platform's software version.

#### `renderDatasetSite($dataset, $additionalData)`

Returns a string corresponding to the compiled HTML, based on the `datasetsite.mustache`, and the provided `$data`.

`$dataset` must be a valid `\OpenActive\Models\SchemaOrg\Dataset` model.

`$additionalData` must be an associative array with the following keys:
- **backgroundImageUrl** - The background image to show on the page;
- **documentationUrl** - The documentation's URL;
- **platformName** - The platform's name;
- **platformUrl** - The platform's URL;
- **softwareVersion** - The platform's software version.

#### `DistributionType`

A class containing the supported distribution types:

| Constant                  | Value                   |
| ------------------------- | ----------------------- |
| `COURSE`                  | `Course`                |
| `COURSE_INSTANCE`         | `CourseInstance`        |
| `EVENT`                   | `Event`                 |
| `FACILITY_USE`            | `FacilityUse`           |
| `HEADLINE_EVENT`          | `HeadlineEvent`         |
| `INDIVIDUAL_FACILITY_USE` | `IndividualFacilityUse` |
| `SCHEDULED_SESSION`       | `ScheduledSession`      |
| `SESSION_SERIES`          | `SessionSeries`         |
| `SLOT`                    | `Slot`                  |

You can use any of the above like this:
```php
use OpenActive\DatasetSiteTemplate\DistributionType;

echo DistributionType::COURSE_INSTANCE;
```

Which will output:
```
CourseInstance
```

## Development

### Installation
```bash
git clone https://github.com/openactive/dataset-site-template-php.git
cd dataset-site-template-php
composer install
```

### Example
From a web server capable of interpreting and compiling PHP, navigate to the `/openactive` folder.

From there you should be able to see the template populated with the JSON-LD data.

The default mustache template (`datasetsite.mustache`) is included under the `src` folder.

In `index.php` you can find an example of the associative array that's going to get parsed by `TemplateRenderer`.

### Running Tests
PHPUnit 4.8 is used to run tests.

To run the whole suite:
```bash
./vendor/bin/phpunit
```

If you want to run the whole suite in verbose mode:
```bash
./vendor/bin/phpunit --verbose
```

You can also run a section of the suite by specifying the class's relative path on which you want to perform tests:
```bash
./vendor/bin/phpunit --verbose tests/Unit/TemplateRendererTest.php
```

For additional information on the commands available for PHPUnit,
consult [their documentation](https://phpunit.de/manual/4.8/en/installation.html)
