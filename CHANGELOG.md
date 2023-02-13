CHANGELOG
=========

2.8.0
-----
* Feature:
  * Extend compatibility for all versions of `symfony/translation` since 3.4.* till 6.2.*.

2.7.0
-----
* Features:
  * Add linter `same_value` which detects translation keys with the same value.
  * Add linter `invalid_value` which detects translation values matching some configured pattern.
  * Implement saving of detected missed translation to separate directory by wrapped Translator.
  * Implement command for the testing if missed translations logged.
* Minors:
  * Add docker configuration for development purposes.
  * Add bash command `bin/dev/remove_reports` for development purposes.
  * Add suggestion to install package symfony/translation.
  * Configured CS Fixer.
  * Mark configuration `insert_missed_keys: ''` deprecated.
    Use `missed_keys: { insert_position: '' }` instead of it.
  * Mark configuration `yaml: { key_pattern: '' }` deprecated.
    Use `linter: { key_valid_pattern: '' }` instead of it.
  * Mark method `\Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder::getLocales()` as deprecated.
    Use `\Aeliot\Bundle\TransMaintain\Service\LocalesDetector::getLocales()` instead of it.
  * Mark method `\Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser::parseFiles()` as deprecated.
    Use `\Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser::parseFiles()` instead of it.
  * Refactored gluing of yaml tree to single level array.
  * Refactored rendering of linters' reports.
  * Rename class `\Aeliot\Bundle\TransMaintain\Model\CsvReader` to `\Aeliot\Bundle\TransMaintain\Model\CSV`.
  * Sort translations files map.
  * Switch name and alias of YAML lint command.
  * Updated package "symfony/translation" version in dev dependencies.
* Bug fixes:
  * Fix braking of translation keys by cleaner when comma is inside key.
  * Fix registering of missed translation.
  * Fix sorting of translation Ids during keys pattern matching.
  * Remove not used dependencies.
* Backward compatibility breaks:
  * Removed deprecated constants:
    * `\Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry::PRESET_ALL`
    * `\Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry::PRESET_BASE`
  * Change naming:
    * language_id -> translation_id
    * language -> locale
  * Move Translator decorators to the separate namespace.
  * Refactored linters' [ReportBag](src/Model/ReportBag.php) class.
  * Removed class `\Aeliot\Bundle\TransMaintain\Report\Builder\ConsoleOutputTableBuilder`.
    Use service `\Aeliot\Bundle\TransMaintain\Service\ReportBagConsoleRenderer` instead of it.
  * Removed linters' report line classes:
    * `\Aeliot\Bundle\TransMaintain\Model\AbstractLine`
    * `\Aeliot\Bundle\TransMaintain\Model\EmptyValueLine`
    * `\Aeliot\Bundle\TransMaintain\Model\FilesMissedLine`
    * `\Aeliot\Bundle\TransMaintain\Model\FilesTransformedLine`
    * `\Aeliot\Bundle\TransMaintain\Model\InvalidValueLine`
    * `\Aeliot\Bundle\TransMaintain\Model\KeysDuplicatedLine`
    * `\Aeliot\Bundle\TransMaintain\Model\KeysMissedLine`
    * `\Aeliot\Bundle\TransMaintain\Model\KeysPatternLine`
    * `\Aeliot\Bundle\TransMaintain\Model\SameValueLine`
  * Removed trait `\Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\GlueKeysTrait`. 
    Use class `\Aeliot\Bundle\TransMaintain\Service\Yaml\KeysLinker` instead of it. 

2.6.0
-----
* Features:
  * Add files allocation functionality to the transform command.
  * Add detector (linter) of empty values of translations.
  * Make Google Translation model configurable.
* Minors:
  * Rename example files (add locale to the file name).
  * Add alias for the Lint YAML command: `aeliot_trans_maintain:yaml:lint`.
  * Move commands which works with YAML files to the separate namespace.
  * Move public constants of `\Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry` to `\Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface`.
  * Simplify report line definitions through the defining of columns order and headers in the single place of each report line model.
  * Use `.env` files to load parameters for the development kernel.

2.5.1
-----
* Minors:
  * Make lint reports easier to understand.

2.5.0
-----
* Features:
  * Add linter "file_transformed" for check if file structure is normalised for all YAML files.
* Minors:
  * Updated parameter data type declaration.
  * Updated phpDocs.
  * Add ability to call commands in the isolated dev environment (without installing into application).
  * Add ability of services testing.

2.4.0
-----
* Features:
  * Implement usage of Google Translate API to translate missed keys.

2.3.2
-----
* Bug fixes:
  * Fix getting of project directory path for Symfony >=5.0.

2.3.1
-----
* Bug fixes:
  * Fix YAML Key Pattern Linter.

2.3.0
-----
* Features:
  * Added compatibility with Symfony 5.2.
  * Added compatibility with Symfony 3.4.
  * Added YAML key pattern checker.
  * Updated Linters presets mechanism.
  * Arguments for command `aeliot_trans_maintain:lint:yaml` became optional. Preset "base" used by default.
  * Added filtering to command `aeliot_trans_maintain:lint:yaml` by options:
    * "domain" - list of analyzed domains,
    * "locale" - list of analyzed locales.

2.2.0
-----
* Features:
  * Implemented additional cases for YAML keys transformation.
* Minors:
  * Extract BrachInjector from key transformer.
  * Update merging of omitted translation keys to YAML files.

2.1.0
-----
* Features:
  * Added console command for the export of omitted translation keys (`aeliot_trans_maintain:yaml:export_missed_translations`).
  * Update translator decorator for hot switching of inserting position by environment variable.

2.0.2
-----
* Features:
  * Downgrade dependency of symfony/translation-contracts.

2.0.1
-----
* Minors:
  * Changed duplicated keys report header.
* Bug fixes:
  * Fixed creation of empty directories.

2.0.0
-----
* Features:
  * Added testing of YAML files.
  * Added translator decorator.

1.0.0
-----
* Features:
  * Initial version based on Symfony 4.4.
  * Implemented YAML files transformation.
  * Added console commands:
      * aeliot_trans_maintain:yaml:transform - files transformer,
      * aeliot_trans_maintain:yaml:sort - separate keys sorter.

