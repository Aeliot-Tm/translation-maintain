Configuration
=============

First aff all, add root node `aeliot_trans_maintain:` to your config files (see [installation](installation.md)).

### Basic configuration:

There is displayed default values of any configuration.

```yaml
aeliot_trans_maintain:
    insert_missed_keys: 'no'      # Switch on/off decorator for the standard translator and define mode of inserting missed keys
    translation_api:
        google:
            key: ~              # your key to the Google Cloud Translate API
            limit: 500000       # limit of symbols per month. Can be null. Limit ignored if value is empty (0 or null)
    yaml:
        indent: 4               # Size of indents in YAML files
```

#### Accepted keys for insert_missed_keys:

- **no** - then decoration switched off.
- **end** - then missed keys will be inserted to the end of file. Mode suitable for now.
- **merge** - *[EXPERIMENTAL]* then keys will be split by dots and merged into the keys tree.

It is recommended to use values: "no" or "end".

### Usage of Environment variable:

Example:

```yaml
# Add parameter TRANS_MAINTAIN_INSERT_MISSED_KEYS=end into .env.local to switch on translator decorator and clear cache
parameters:
    env(TRANS_MAINTAIN_INSERT_MISSED_KEYS): 'no'
    env(GOOGLE_TRANSLATE_API_KEY): ~

aeliot_trans_maintain:
    insert_missed_keys: '%env(TRANS_MAINTAIN_INSERT_MISSED_KEYS)%'
    translation_api:
        google:
            key: '%env(GOOGLE_TRANSLATE_API_KEY)%'
```

After that you can easily switch on/off translator decorator and inserting of missed translation keys by adding/changing of parameter 
`TRANS_MAINTAIN_INSERT_MISSED_KEYS=end` in .env.local file in the project folder.

You can get more information in the [official document](https://symfony.com/doc/current/configuration/env_var_processors.html).


---
*[Read Me](../README.md)*
