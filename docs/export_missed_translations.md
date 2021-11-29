Export missed translations
==========================

There is implemented command for the export of missed translations:

```shell
php bin/console aeliot_trans_maintain:yaml:export_missed_translations DOMAIN SOURCE_LOCALE TARGET_LOCALE
```

Arguments:

- **domain** - domain used for search
- **source_locale** - locale used as a source
- **target_locale** - locale there missed some keys

It returns nested translation keys as joined per dots with existing translations as a part of YAML file. So, output can be copy-pasted ease.

```shell
some.nested.key: 'Translation 1'
another.key: 'Translation 2'
this_is_a_key_too: 'translation 3'
```

Example of the call:
```shell
php bin/console aeliot_trans_maintain:yaml:export_missed_translations messages en de
```