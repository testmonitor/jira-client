# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.0.0] - 2025-09-17
### Added
- Better test coverage for updating issues, creating / updating webhooks
  
### Updated
- Replaced issues endpoint due to Jira deprecation
- Introduced TokenPaginatedResponse
- Refactored PaginatedResponse to LengthAwarePaginatedResponse
- Fix null-value code style

## [2.3.0] - 2025-02-13
### Updated
- Replaced Travis / Scrutinizer with Codecov
- Fixed refresh token scope handling

## [2.2.0] - 2024-05-14
### Updated
- Improved exception handling
- Revised token refresh handling
- Updated ADF tools to version 1.2

## [2.1.0] - 2024-04-19
### Updated
- Replaced ADF tools
- Fixed parsing scaled images in descriptions

## [2.0.0] - 2024-04-10
### Added
- PHP 8.2 support
- oAuth support
- JQL support
- ADF support for issue descriptions
- Update and transition issues
- Managing webhooks
- Managing projects
- Managing project versions
- Managing issue priorities
- Managing issue types
- Managing issue statuses

### Updated
- Managing issues

### Removed
- PHP 8.0 support
- Atlassian Jira Server support

## [1.4.0] - 2023-04-10
### Changed
- Updated Lesstif PHP Jira client

## [1.3.0] - 2021-01-07
### Changed
- Updated Lesstif PHP Jira client

## [1.2.0] - 2020-05-06
### Updated
- Updated composer.json namespace to comply with PSR-4

## [1.1.0] - 2020-03-08
### Changed
- Updated Lesstif PHP Jira client

## [1.0.0] - 2019-12-09
### Added
- Initial version.
