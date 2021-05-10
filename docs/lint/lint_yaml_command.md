Lint YAML translation files
===========================

Command:
```shell
php bin/console aeliot_trans_maintain:lint:yaml <key1> <key2> <keyN>
```
It returns 0 if there are no problems detected and 1 otherwise.
Reports of each linter return into STD_OUT.
You can pass preset names and/or keys of linters.
Each linter will be called only once even its name passed as an argument several times.
The order of passed keys has no matter.

### Presets
- **base** - then executes base (mostly required) linters. Can be used with the list of linters.
- **all** - *[RESERVED]* then executes all linters. Applicable not for all projects. Only one key can be posted in this case.

### Linters

#### Base linters

- **files_missed** - then check if domain presented by all mentioned locales.
- **keys_duplicated** - then check if project has duplicated keys in each used locale of each domain.
- **keys_missed** - then check if key mentioned in any locale of domain presented in all of them.


### Examples of reports

#### Missed files

```shell
+------------+-------------------+
| domain     | omitted_languages |
+------------+-------------------+
| messages   | de                |
| validators | fr, pl            |
+------------+-------------------+
```

#### Duplicated keys

```shell
+----------+--------+------------------------+
| domain   | locale | duplicated_language_id |
+----------+--------+------------------------+
| messages | en     | some.nested.key        |
+----------+--------+------------------------+
```

#### Missed translation keys

```shell
+----------+-------------+-------------------+
| domain   | language_id | omitted_languages |
+----------+-------------+-------------------+
| messages | first_key   | fr, nl            |
| messages | second_key  | fr, de            |
+----------+-------------+-------------------+
```

---
*[Read Me](../../README.md)*
