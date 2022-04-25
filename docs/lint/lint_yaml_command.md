Lint YAML translation files
===========================

Command:
```shell
php bin/console aeliot_trans_maintain:lint:yaml KEY_1 KEY_2 KEY_N
```

You can pass preset name and/or key(s) of linters.
Each linter will be called only once even its name passed as an argument several times or is part of a preset.
The order of passed keys has no matter. Linters will be executed according to the internal sorting.

The command returns:
- 0 if there is problem detected
- 1 otherwise. 

So, you can use it in CI testing scripts easy. Reports of each linter will be returned into STD_OUT.

### Presets
- **base** - executes base (mostly required) linters. Can be used with the list of linters.
- **all** - executes all linters. Applicable not for all projects. Only one (this) key permitted if passed.

### Linters

- _Base linters:_
  - **files_missed** - check if domain presented by all mentioned locales. [Report example](./reports/files_missed.md).
  - **keys_duplicated** - check if project has duplicated keys in each used locale of each domain. [Report example](./reports/files_missed.md).
  - **keys_missed** - check if key mentioned in one locale of the domain is not presented in others. [Report example](./reports/files_missed.md).

- _Auxiliary linters:_
  - **empty_value** - then check if translation is empty string. Note: value trimmed before the testing. So, string that consists of spaces is empty too.
  - **file_transformed** - then check if translation files transformed (has normalised structure).
  - **key_pattern** - then check if translation keys match configured pattern. Example: `/^[a-zA-Z0-9_.-]+$/`.
  - **same_value** - detect translations with the same values in all files of one locale of one domain.

---
*[Read Me](../../README.md)*
