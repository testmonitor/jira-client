# TestMonitor Jira Client

[![Latest Stable Version](https://poser.pugx.org/testmonitor/jira-client/v/stable)](https://packagist.org/packages/testmonitor/jira-client)
[![CircleCI](https://img.shields.io/circleci/project/github/testmonitor/jira-client.svg)](https://circleci.com/gh/testmonitor/jira-client)
[![Travis Build](https://travis-ci.com/testmonitor/jira-client.svg?branch=master)](https://travis-ci.com/testmonitor/jira-client)
[![Code Quality](https://scrutinizer-ci.com/g/testmonitor/jira-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/testmonitor/jira-client/?branch=master)
[![StyleCI](https://styleci.io/repos/222276101/shield)](https://styleci.io/repos/222276101)
[![License](https://poser.pugx.org/testmonitor/jira-client/license)](https://packagist.org/packages/testmonitor/jira-client)

This package provides a very basic, convenient, and unified wrapper for the [PHP JIRA Rest Client](https://github.com/lesstif/php-jira-rest-client). 

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Examples](#examples)
- [Tests](#tests)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)
  
## Installation

To install the client you need to require the package using composer:

	$ composer require testmonitor/jira-client

Use composer's autoload:

```php
require __DIR__.'/../vendor/autoload.php';
```

You're all set up now!

## Usage

You'll have to instantiate the client using your credentials:

```php
$jira = new \TestMonitor\Jira\Client('https://myjira.atlassian.net', 'username', 'password');
```

Next, you can start interacting with Jira. 

## Examples

Get a list of Jira projects:

```php
$projects = $jira->projects();
```

Or creating an issue, for example (using type 'Bug' and project key 'PROJ'):

```php
$issue = $jira->createIssue(new \TestMonitor\Jira\Resources\Issue(
    'Some issue',
    'A better description',
    'Bug',
    'PROJ'
));
```

## Tests

The package contains integration tests. You can run them using PHPUnit.

    $ vendor/bin/phpunit
    
## Changelog

Refer to [CHANGELOG](CHANGELOG.md) for more information.

## Contributing

Refer to [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

## Credits

* **Thijs Kok** - *Lead developer* - [ThijsKok](https://github.com/thijskok)
* **Stephan Grootveld** - *Developer* - [Stefanius](https://github.com/stefanius)
* **Frank Keulen** - *Developer* - [FrankIsGek](https://github.com/frankisgek)
* **Muriel Nooder** - *Developer* - [ThaNoodle](https://github.com/thanoodle)

## License

The MIT License (MIT). Refer to the [License](LICENSE.md) for more information.
