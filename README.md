# dataset-site-template-php
PHP Classes and resources supporting dataset site creation

Tools intended to simplify creation of dataset sites using templates.

For comparison, see the [.NET](https://github.com/openactive/dataset-site-template-example-dotnet) and [Ruby](https://github.com/openactive/dataset-site-template-ruby) implementations.

## Table Of Contents
- [Requirements](#requirements)
- [Usage](#usage)
    - [API](#api)
        - [`renderSimpleDatasetSite($settings, $supportedFeedTypes)`](#rendersimpledatasetsitesettings-supportedfeedtypes)
        - [`renderSimpleDatasetSiteFromDataDownloads($settings, $dataDownloads, $dataFeedDescriptions)`](#rendersimpledatasetsitefromdatadownloadssettings-datadownloads-datafeeddescriptions)
        - [`renderDatasetSite($dataset)`](#renderdatasetsitedataset)
        - [`FeedType`](#feedtype)
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
echo (new TemplateRenderer())->renderSimpleDatasetSite($settings, $supportedFeedTypes);
```

Where `$settings` could be defined like the following (as an example):
```php
$settings = array(
    "backgroundImageUrl" => "https://ourparks.org.uk/bg.jpg",
    "bookingServiceName" => "AcmeBooker",
    "bookingServiceSoftwareVersion" => "0.1.0",
    "bookingServiceUrl" => "https://acmebooker.example.com/",
    "datasetSiteDiscussionUrl" => "https://github.com/ourparks/opendata",
    "datasetSiteUrl" => "https://ourparks.org.uk/openactive",
    "documentationUrl" => "https://ourparks.org.uk/openbooking/",
    "email" => "hello@ourparks.org.uk",
    "legalEntity" => "Our Parks",
    "name" => "Our Parks Sessions",
    "openDataBaseUrl" => "https://ourparks.org.uk/opendata/",
    "organisationLogoUrl" => "https://ourparks.org.uk/logo.png",
    "organisationName" => "Our Parks",
    "organisationUrl" => "https://ourparks.org.uk/",
    "plainTextDescription" => "Our Parks - turn up tone up!",
);
```

And `$feedTypes` could be defined as:
```php
use OpenActive\DatasetSiteTemplate\FeedType;

$feedTypes = array(
    FeedType::FACILITY_USE,
    FeedType::SCHEDULED_SESSION,
    FeedType::SESSION_SERIES,
    FeedType::SLOT,
);
```

### API

#### `renderSimpleDatasetSite($settings, $supportedFeedTypes)`

Returns a string corresponding to the compiled HTML, based on the `datasetsite.mustache`, the provided `$settings`, and `$supportedFeedTypes`.

`$settings` must contain the following keys:

##### Settings

| Key                             | Type     | Description |
| ------------------------------- | -------- | ----------- |
| `backgroundImageUrl`            | `string` | The background image to show on the page |
| `bookingServiceName`            | `string` | The platform's name |
| `bookingServiceSoftwareVersion` | `string` | The platform's software version. |
| `bookingServiceUrl`             | `string` | The platform's URL |
| `datasetSiteDiscussionUrl`      | `string` | The discussion URL for the dataset |
| `datasetSiteUrl`                | `string` | The dataset site URL |
| `documentationUrl`              | `string` | The documentation's URL |
| `email`                         | `string` | The email of the publisher of this dataset |
| `legalEntity`                   | `string` | The legal name of the publisher of this dataset |
| `name`                          | `string` | The name of the publisher of this dataset |
| `openDataBaseUrl`               | `string` | The base OpenData URL for this dataset, used as a base URL for the feeds |
| `organisationLogoUrl`           | `string` | A valid image URL of the organisation's logo |
| `organisationName`              | `string` | The organisation's name |
| `organisationUrl`               | `string` | The organisation's URL |
| `plainTextDescription`          | `string` | The publisher's description in plain text |

And `$supportedFeedTypes` must be an `array` of `FeedType` constants. See [available types](#feedtype)

#### `renderSimpleDatasetSiteFromDataDownloads($settings, $dataDownloads, $dataFeedDescriptions)`

Returns a string corresponding to the compiled HTML, based on the `datasetsite.mustache`, the provided [`$settings`](#settings), `$dataDownloads` and `$dataFeedDescriptions`.

The `$dataDownloads` argument must be an `array` of `\OpenActive\Models\OA\DataDownload` objects.

The `$dataFeedDescriptions` must be an array of strings.

This gets calculated internally by the `renderSimpleDatasetSite` method as the `displayName` attributes of the `FeedConfiguration`s matching the `$supportedFeedTypes` (removing false-y values and duplicates).

For example, assuming `$supportedFeedTypes` is defined as:
```php
$supportedFeedTypes = array(
    FeedType::FACILITY_USE,
    FeedType::SCHEDULED_SESSION,
    FeedType::SESSION_SERIES,
    FeedType::SLOT,
);
```

The resulting `$dataFeedDescriptions` will be:

```php
$dataFeedDescriptions = array(
    "Sessions",
    "Facilities"
);
```

#### `renderDatasetSite($dataset)`

Returns a string corresponding to the compiled HTML, based on the `datasetsite.mustache`, and the provided `$dataset`.

The `$dataset` argument must be an object of type `\OpenActive\Models\OA\Dataset`.

#### `FeedType`

A class containing the supported distribution types:

| Constant                  | Value                   |
| ------------------------- | ----------------------- |
| `COURSE`                  | `Course`                |
| `COURSE_INSTANCE`         | `CourseInstance`        |
| `EVENT`                   | `Event`                 |
| `EVENT_SERIES`            | `EventSeries`           |
| `FACILITY_USE`            | `FacilityUse`           |
| `HEADLINE_EVENT`          | `HeadlineEvent`         |
| `INDIVIDUAL_FACILITY_USE` | `IndividualFacilityUse` |
| `SCHEDULED_SESSION`       | `ScheduledSession`      |
| `SESSION_SERIES`          | `SessionSeries`         |
| `SLOT`                    | `Slot`                  |

You can use any of the above like this:
```php
use OpenActive\DatasetSiteTemplate\FeedType;

echo FeedType::COURSE_INSTANCE;
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
