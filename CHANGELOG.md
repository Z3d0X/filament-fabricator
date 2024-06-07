# Changelog

All notable changes to `filament-fabricator` will be documented in this file.

## v2.2.2 - 2024-05-12

### What's Changed

* build(deps): bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/152
* build(deps): bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/156
* Allow string IDs by @rojtjo in https://github.com/Z3d0X/filament-fabricator/pull/158

### New Contributors

* @rojtjo made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/158

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.2.1...v2.2.2

## v2.2.1 - 2024-04-15

### What's Changed

* build(deps): bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/148
* Add note to README regarding the plugin assets by @pboivin in https://github.com/Z3d0X/filament-fabricator/pull/149
* Fixed incorrect table name during migration by @witaway in https://github.com/Z3d0X/filament-fabricator/pull/151

### New Contributors

* @pboivin made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/149
* @witaway made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/151

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.2.0...v2.2.1

## v2.2.0 - 2024-03-12

### Laravel 11.x compatibility added

#### What's Changed

* build(deps): bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/143
* Laravel 11.x Compatibility by @laravel-shift in https://github.com/Z3d0X/filament-fabricator/pull/142

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.1.1...v2.2.0

## v2.1.1 - 2024-02-19

### What's Changed

* Fix Resource Registration by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/140

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.1.0...v2.1.1

## v2.1.0 - 2024-02-09

### What's Changed

* Fix Slug Unique Constraint by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/135
* Feature Block Picker Styles by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/136

Note: to apply the fix for unique slug issue from #135 please publish the migrations using `php artisan vendor:publish --tag=filament-fabricator-migrations` & then run migrations

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.6...v2.1.0

## v2.0.6 - 2024-02-03

### What's Changed

* Fix octane issues change registering package from scoped to singleton by @ksimenic in https://github.com/Z3d0X/filament-fabricator/pull/130

### New Contributors

* @ksimenic made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/130

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.5...v2.0.6

## v2.0.5 - 2024-01-18

### What's Changed

* Respect app locale for slug generation by @flolanger in https://github.com/Z3d0X/filament-fabricator/pull/109
* build(deps): bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/124
* [ar]: Add Arabic translations by @mohamedsabil83 in https://github.com/Z3d0X/filament-fabricator/pull/127

### New Contributors

* @flolanger made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/109
* @mohamedsabil83 made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/127

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.4...v2.0.5

## v2.0.4 - 2023-11-26

### What's Changed

* Update README.md by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/105
* Fix getLabel() and getPluralLabel() deprecation by @devhoussam1998 in https://github.com/Z3d0X/filament-fabricator/pull/113
* Allow easier subclassing of the Page model by @Voltra in https://github.com/Z3d0X/filament-fabricator/pull/115

### New Contributors

* @devhoussam1998 made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/113
* @Voltra made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/115

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.3...v2.0.4

## v2.0.3 - 2023-10-14

### What's Changed

- build(deps): bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/97
- fix: default layout resolving by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/103

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.2...v2.0.3

## v1.2.2 - 2023-10-14

### What's Changed

- fix: default layout resolving by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/103

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.2.1...v1.2.2

## v2.0.2 - 2023-09-25

### What's Changed

- [Docs]: Remove Peek from installation instructions by @viraljetani in https://github.com/Z3d0X/filament-fabricator/pull/87
- build(deps): bump actions/checkout from 3 to 4 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/89
- Update PageResource.php by @RibesAlexandre in https://github.com/Z3d0X/filament-fabricator/pull/93
- Fix: Lazy Loading in Model Strict Mode by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/96

### New Contributors

- @viraljetani made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/87
- @RibesAlexandre made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/93

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.1...v2.0.2

## v2.0.1 - 2023-08-20

### What's Changed

- Fix deprecated code by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/83

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v2.0.0...v2.0.1

## v1.1.5 - 2023-04-18

### What's Changed

- Prevented lazy loading. by @danielbehrendt in https://github.com/Z3d0X/filament-fabricator/pull/56

### New Contributors

- @danielbehrendt made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/56

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.1.4...v1.1.5

## v1.1.4 - 2023-03-30

### What's Changed

- build(deps): bump aglipanci/laravel-pint-action from 2.1.0 to 2.2.0 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/54
- Translatable Resource Labels in https://github.com/Z3d0X/filament-fabricator/commit/073fb9d4935951b6f59fd01b8426ddf8321344be

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.1.3...v1.1.4

## v1.1.3 - 2023-03-05

### What's Changed

- Fix: Page Middleware by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/51

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.1.2...v1.1.3

## v1.1.2 - 2023-02-27

### What's Changed

- Make `$page` instance accessible in blocks by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/45
- Shorter Commands by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/46
- Fix Routing Urls by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/47

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.1.1...v1.1.2

## v1.1.1 - 2023-02-17

### What's Changed

- Fix: ignore deleted blocks by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/41
- Refactor to PageController by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/42

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.1.0...v1.1.1

## v1.1.0 - 2023-02-15

### What's Changed

- Laravel 10 Support by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/38

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.0.4...v1.1.0

## v1.0.4 - 2023-02-14

### What's Changed

- build(deps): bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/35
- Fix custom PageResource not being considered on Pages by @lucasgiovanny in https://github.com/Z3d0X/filament-fabricator/pull/37

### New Contributors

- @lucasgiovanny made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/37

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.0.3...v1.0.4

## v1.0.3 - 2023-01-07

### What's Changed

- build(deps): bump ramsey/composer-install from 1 to 2 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/28
- Add a save button at the top of the page by @jvkassi in https://github.com/Z3d0X/filament-fabricator/pull/31
- build(deps): bump aglipanci/laravel-pint-action from 1.0.0 to 2.1.0 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/32
- Configurable Database Table & Migration Down Method by @mrlinnth in https://github.com/Z3d0X/filament-fabricator/pull/19
- Fix: Frontend Routing by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/34

### New Contributors

- @jvkassi made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/31
- @mrlinnth made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/19

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.0.2...v1.0.3

## v1.0.2 - 2022-11-14

### What's Changed

- Fix: Homepage Routing by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/24

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.0.1...v1.0.2

## v1.0.1 - 2022-11-13

### What's Changed

- build(deps): bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/21
- Feature: Configurable `PageResource` by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/22
- Fix: Hide page urls when routing disabled by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/23

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v1.0.0...v1.0.1

## v1.0.0 - 2022-10-25

### What's Changed

- Feature: `PageResource` Translations by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/7
- Feature: `PageBuilder` Field by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/8
- Feature: Base Layout by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/10
- Feature: Configurable `Page` Model by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/11
- Fix: HomePage Routing by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/12
- build(deps): bump dependabot/fetch-metadata from 1.3.3 to 1.3.4 by @dependabot in https://github.com/Z3d0X/filament-fabricator/pull/15
- Feature: Nested Pages by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/13
- adding prefix for pages by @MuhamadSelim in https://github.com/Z3d0X/filament-fabricator/pull/14
- fixing Dynamic Page Model for FilamentFabricatorManager.php setPageUr… by @MuhamadSelim in https://github.com/Z3d0X/filament-fabricator/pull/16
- Feature: Page Model Contract by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/17
- Feature: PageResource custom fieldslots by @Z3d0X in https://github.com/Z3d0X/filament-fabricator/pull/18

### New Contributors

- @Z3d0X made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/7
- @dependabot made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/15
- @MuhamadSelim made their first contribution in https://github.com/Z3d0X/filament-fabricator/pull/14

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/compare/v0.1.0...v1.0.0

## v0.1.0 - 2022-09-19

**Full Changelog**: https://github.com/Z3d0X/filament-fabricator/commits/v0.1.0

## 1.0.0 - 202X-XX-XX

- initial release
