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
1. Update all YAML files in the directory:
   ```shell
   find PATH_TO_DIRECTORY -type f \( -iname \*.yml -o -iname \*.yaml \) | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
   ```
Full information about updating of YAML files see [there](docs/transform_yaml_files.md).

**NOTE:** There used standard `\Symfony\Component\Yaml\Yaml` class for dumping, so it inserts single-word values without escaping.

---
*You can help to implement more features :) See plans [there](TODO.md).*
