# UDB Helpers for PHP Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.6.2 - 2025-04-04
### Added
- Added an optional "destination" parameter to the authUrl, to facilitate automatically updating tokens.


## 1.6.1 - 2025-03-07
### Fixed
- Fixed a bug in the `delete` function

## 1.6.0 - 2024-03-01
### Added
- Before creating a new place, we now search for an exact match and if that exists we don't create the new place (following documentation [here](https://docs.publiq.be/docs/uitdatabank/entry-api/places/finding-and-reusing-places))

## 1.5.0 - 2023-08-24
### Added
- Added ``delete`` function

## 1.4.0 - 2023-05-16
### Added
- Added ``updatePlaceWorkflowStatus`` function

## 1.3.0 - 2023-05-12
### Added
- Added ``updateWorkflowStatus`` function

## 1.2.0 - 2023-04-19
### Added
- Added ``createMediaObject`` function

## 1.1.0 - 2023-04-13
### Added
- Added functionality to create and update an UDB Event

## 1.0.0 - 2023-03-30
### Added
- Initial release