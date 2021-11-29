Machine Translation via Vendor's API
===================================

There is implemented usage of Google Claud Translation.
Add credentials to the [configuration](configuration.md) for the using of machine translation.
After that, you can execute command:
```shell
php bin/console aeliot_trans_maintain:yaml:translate --domain=messages --source_locale=en --target_locale=de
```
or short mode
```shell
php bin/console a:y:translate -d messages -s en -t de
```

Here:
- **domain** - one o several domains for translation. There may be several options.
- **source_locale** - locale there existing translations will be taken. Should be single.
- **target_locale** - locale with missing translations, and new ones will be inserted there. There may be several options.

**NOTE**! Exception will be thrown if target locale has no missed translations in compare with source location.

---
*[Read Me](../README.md)*
