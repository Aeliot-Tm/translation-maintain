TransMaintain
=============

Package which helps to keep you translations consistent. Compatible with Symfony versions since 3.4.

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

---
*You can help to implement more features :) See plans [there](TODO.md).*
