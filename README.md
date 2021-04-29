# TransMaintain
Package which help to keep you translations consistent.

## Installation

Execute command:

```shell
$ composer require --dev aeliot-tm/translation-maintain
```

## Usage

1. Update one YAML file
   ```shell
   $ php bin/console aeliot_trans_maintain:yaml:transform <path_to_file_to_be_updated>
   ```
1. Update all files in the directory
   ```shell
   $ find path_to_directory -type f \( -iname \*.yml -o -iname \*.yaml \) | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
   ```
1. Test your translation files. Execute command:
   ```shell
   $ php bin/console aeliot_trans_maintain:lint:yaml all
   ```
   See additional information [there](docs/lint/lint_yaml_command.md).


---
*You can help to implement more features :) See plans [there](TODO.md).*