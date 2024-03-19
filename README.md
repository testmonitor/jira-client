# TestMonitor Jira Client

[![Latest Stable Version](https://poser.pugx.org/testmonitor/jira-client/v/stable)](https://packagist.org/packages/testmonitor/jira-client)
[![CircleCI](https://img.shields.io/circleci/project/github/testmonitor/jira-client.svg)](https://circleci.com/gh/testmonitor/jira-client)
[![Travis Build](https://travis-ci.com/testmonitor/jira-client.svg?branch=master)](https://app.travis-ci.com/github/testmonitor/jira-client)
[![Code Coverage](https://scrutinizer-ci.com/g/testmonitor/jira-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/testmonitor/jira-client/?branch=master)
[![Code Quality](https://scrutinizer-ci.com/g/testmonitor/jira-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/testmonitor/jira-client/?branch=master)
[![StyleCI](https://styleci.io/repos/222957448/shield)](https://styleci.io/repos/222957448)
[![License](https://poser.pugx.org/testmonitor/jira-client/license)](https://packagist.org/packages/testmonitor/jira-client)

This package provides a very basic, convenient, and unified wrapper for Jira.

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

This client only supports **oAuth authentication**. You'll need an Atlassian Jira application to proceed. If you haven't done so,
please read up with the [Jira authentication docs](https://developer.atlassian.com/console/myapps/) on how
to create an application.

When your Jira application is up and running, start with the oAuth authorization:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$jira = new \TestMonitor\Jira\Client($oauth);

header('Location: ' . $jira->authorizationUrl());
exit();
```

This will redirect the user to a page asking confirmation for your app getting access to Jira. Make sure your redirectUrl points
back to your app. This URL should point to the following code:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$jira = new \TestMonitor\Jira\Client($oauth);

$token = $jira->fetchToken($_REQUEST['code']);
```

When everything went ok, you should have an access token (available through Token object).

For any subsequent action, you'll need to retrieve your cloud ID to proceed:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$token = new \TestMonitor\Jira\AccessToken('eyJ0...', '0/34ccc...', 1574601877); // the token you got last time
$jira = new \TestMonitor\Jira\Client($oauth, null, $token);

$account = $jira->account();
```

Use the cloud ID as a parameter when instantiating the client:

```php
$oauth = [
    'clientId' => '12345',
    'clientSecret' => 'abcdef',
    'redirectUrl' => 'https://redirect.myapp.com/',
];

$token = new \TestMonitor\Jira\AccessToken('eyJ0...', '0/34ccc...', 1574601877);
$jira = new \TestMonitor\Jira\Client($oauth, $account->id, $token);
```

That's it!

Please note that the access token will be valid for **one hour**. When it expires, you'll need to refresh it:

```php
if ($token->expired()) {
    $newToken = $jira->refreshToken();
}
```

The new token will be valid again for the next hour.

## Examples

Retrieve the details for a project using its key:

```php
$project = $jira->project('KEY');
```

Or create a new issue using the first available issue type:

```php
$issueTypes = $jira->issueTypes('KEY');

$issue = $jira->createIssue(new \TestMonitor\Jira\Resources\Issue([
    'summary' => 'It is time Marty!',
    'description' => 'Great Scot!',
    'project' => $project,
    'type' => $issueType[0],
]));
```

## Tests

The package contains integration tests. You can run them using PHPUnit.

    $ vendor/bin/phpunit


## Changelog

Refer to [CHANGELOG](CHANGELOG.md) for more information.

## Contributing

Refer to [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

## Credits

- **Thijs Kok** - _Lead developer_ - [ThijsKok](https://github.com/thijskok)
- **Stephan Grootveld** - _Developer_ - [Stefanius](https://github.com/stefanius)
- **Frank Keulen** - _Developer_ - [FrankIsGek](https://github.com/frankisgek)

## License

The MIT License (MIT). Refer to the [License](LICENSE.md) for more information.
