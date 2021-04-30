# Lint YAML translation files

Command:
```shell
$ php bin/console aeliot_trans_maintain:lint:yaml <key1> <key2> <keyN>
```
It returns 0 if there are no problems detected and 1 otherwise.

### Accepted keys:
- **all** - *[RESERVED]* then executes all linters. Applicable not for all projects. The same as **base** for now. Only one key can be posted in this case. 
- **base** - then executes base (mostly required) linters. Only one key can be posted in this case.
- **files_missed** - then check if domain presented by all mentioned locales.
- **keys_duplicated** - then check if project has duplicated keys in each used locale of each domain.
- **keys_missed** - then check if key mentioned in any locale of domain presented in all of them.


---
*[Read Me](../../README.md)*
