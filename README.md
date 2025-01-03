TransMaintain
=============

[![GitHub Release](https://img.shields.io/github/v/release/Aeliot-Tm/translation-maintain?label=Release&labelColor=black)](https://packagist.org/packages/aeliot-tm/translation-maintain)
[![WFS](https://github.com/Aeliot-Tm/translation-maintain/actions/workflows/automated_testing.yml/badge.svg?branch=main)](https://github.com/Aeliot-Tm/translation-maintain/actions)
[![GitHub Issues or Pull Requests](https://img.shields.io/github/issues-pr-closed/Aeliot-Tm/php-cs-fixer-baseline?label=Pull%20Requests&labelColor=black)](https://github.com/Aeliot-Tm/php-cs-fixer-baseline/pulls?q=is%3Apr+is%3Aclosed)
[![GitHub License](https://img.shields.io/github/license/Aeliot-Tm/php-cs-fixer-baseline?label=License&labelColor=black)](LICENSE)

TransMaintain helps to keep you translations consistent. It gives handy tools for their management and control on CI.

Compatible with all Symfony versions since: 3.4.

## Installation

Basically, if you use Flex there is enough to execute the command:

```shell
composer require --dev aeliot-tm/translation-maintain
```
See additional information about installation [there](docs/installation.md) and description of [configuration](docs/configuration.md).

## Usage

### Testing of translation files

Test your YAML translation files:
   ```shell
   php bin/console aeliot_trans_maintain:lint:yaml base
   ```
Full information about files transformation see [there](docs/lint/lint_yaml_command.md).

### Update YAML files

1. Update certain YAML file:
   ```shell
   php bin/console aeliot_trans_maintain:yaml:transform <PATH_TO_FILE_TO_BE_UPDATED>
   ```
2. Update all YAML files in the project:
   ```shell
   php bin/console aeliot_trans_maintain:yaml:transform --all
   ```
2. Update some YAML files of the project which belongs to domain(s) and/or locale(s):
   ```shell
   php bin/console aeliot_trans_maintain:yaml:transform --domain=messages --domain=validators --locale=en --locale=de
   ```
3. Update all YAML files in the specific directory (e.g. not standard or not in the project):
   ```shell
   find PATH_TO_DIRECTORY -type f \( -iname \*.yml -o -iname \*.yaml \) | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
   ```
   You can filter them additionally with `grep "some text in the file path"` when you add this before, after or instead of `sort` instruction.
   And don't forget to separate instructions by the pipe.

Additional information about updating of YAML files see [there](docs/transform_yaml_files.md).

### Export missed translations

Example:
   ```shell
   php bin/console aeliot_trans_maintain:yaml:export_missed_translations messages en de
   ```
Full information about files transformation see [there](docs/export_missed_translations.md).

### Machine Translation via Vendor's API

Full information about machine translation see [there](docs/machine_translation.md).

**NOTE:** There used standard `\Symfony\Component\Yaml\Yaml` class for dumping, so it inserts single-word values without escaping.

### Additional description

Article on the Habr (ru): https://habr.com/ru/articles/555954/


---
*You can help to implement more features :) See plans [there](TODO.md).*
