# Sentry Changelog

All notable changes to this project will be documented in this file.

## Unreleased

### Changed
- Improved settings templates

### Fixed
- Excluded status codes being ignored if values included a whitespace ([#5](https://github.com/lukeyouell/craft-sentry/issues/5))
- Potential for twig to error if only the auth token is saved ([#4](https://github.com/lukeyouell/craft-sentry/issues/4))

## 1.3.3 - 2018-04-25

### Added
- Added defensive code for `CRAFT_ENVIRONMENT`

## 1.3.2 - 2018-03-12

### Changed
- Set Craft CMS minimum requirement to `^3.0.0-RC11`
- Set Sentry minimum requirement to `^1.8.3`

## 1.3.1 - 2018-02-09

### Fixed
- Missing settings for `excludedCodes`

## 1.3.0 - 2018-02-09

### Added
- Option to ignore certain error codes [#2](https://github.com/lukeyouell/craft-sentry/issues/2)
- Status code now passed as additional data

### Changed
- Settings template tweaks

## 1.2.1 - 2018-02-09

### Changed
- Renamed the composer package name to `craft-sentry`

## 1.2.0 - 2018-02-08

### Added
- Multi-environment support

## 1.1.2 - 2018-02-08

### Added
- 'Get Authentication Token' button

### Changed
- GitHub links reflect repo name & branch change

## 1.1.1 - 2017-12-15

### Fixed
- Craft 3 composer requirement

## 1.1.0 - 2017-12-01

### Added
- API Support, which allows you to select a Project & Client DSN from the CP (authentication token with `project:read` required)

## 1.0.1 - 2017-11-29

### Added
- Icon in png format

### Fixed
- Spelling mistakes

## 1.0.0 - 2017-11-29

### Added
- Initial release
