# TransMaintain
Package which helps to keep you translations consistent.

## Installation

Basically, if you use Flex there is enough to execute the command:

```shell
$ composer require --dev aeliot-tm/translation-maintain
```
See additional information about installation [there](docs/installation.md) and description of [configuration](docs/configuration.md).

## Usage

1. Test your translation YAML files (see additional information [there](docs/lint/lint_yaml_command.md)):
   ```shell
   $ php bin/console aeliot_trans_maintain:lint:yaml all
   ```
1. Update certain YAML file:
   ```shell
   $ php bin/console aeliot_trans_maintain:yaml:transform <path_to_file_to_be_updated>
   ```
1. Update all YAML files in the directory:
   ```shell
   $ find path_to_directory -type f \( -iname \*.yml -o -iname \*.yaml \) | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
   ```


**NOTE:** There used standard `\Symfony\Component\Yaml\Yaml` class for dumping, so it inserts single-word values without escaping.

---
*You can help to implement more features :) See plans [there](TODO.md).*
