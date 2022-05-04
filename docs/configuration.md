Configuration
=============

First aff all, add root node `aeliot_trans_maintain:` to your config files (see [installation](installation.md)).

### Basic configuration:

There is enough to add base section to configuration file then default values will be used.

```yaml
aeliot_trans_maintain: 
```

### Description of configuration options:

```yaml
aeliot_trans_maintain:
    insert_missed_keys: 'no'        # Deprecated! Use: "missed_keys: { insert_position: '' }"
    linter:
        key_valid_pattern: ~        # Pattern to match valid translation keys. Example: /^[a-zA-Z0-9_.-]+$/
        value_invalid_pattern: ~    # Pattern to match invalid translations. Example: /[\x00-\x07]/
    missed_keys:
        directory: ~                # Path to directory where missed translation values will be saved.
        insert_position: 'no'       # Switch on/off decorator for the standard translator and define mode of inserting missed keys.
    translation_api:
        google:
            key: ~              # Your key to the Google Cloud Translate API
            limit: 500000       # Limit of symbols per month. Can be null. Limit ignored if value is empty (0 or null)
            model: 'base'       # Used model of translation
    yaml:
        indent: 4               # Size of indents in YAML files
        key_pattern: ~          # Deprecated! Use: "linter: { key_valid_pattern: '' }"
```

#### Permitted positions for missed keys:

- **no** - then decoration of base translation will be switched off. It will not look for missed translation keys.
- **end** - then missed keys will be inserted to the end of file.
- **merge** - then keys will be split by dots and merged into the keys tree (translation file will be transformed file after the insertion).

It is recommended to use values: "no" or "end".

### Using of Environment variables:

Example:

```yaml
# Add parameter TRANS_MAINTAIN_INSERT_MISSED_KEYS=end into .env.local to switch on translator decorator and clear cache
parameters:
    env(TRANS_MAINTAIN_INSERT_MISSED_KEYS): 'no'
    env(GOOGLE_TRANSLATE_API_KEY): ~

aeliot_trans_maintain:
    missed_keys:
        insert_position: '%env(TRANS_MAINTAIN_INSERT_MISSED_KEYS)%'
    translation_api:
        google:
            key: '%env(GOOGLE_TRANSLATE_API_KEY)%'
```

After that you can easily switch on/off translator decorator and inserting of missed translation keys by adding/changing of parameter 
`TRANS_MAINTAIN_INSERT_MISSED_KEYS=end` in .env.local file in the project folder.

You can get more information about the using of environment variables in the [Symfony documentation](https://symfony.com/doc/current/configuration/env_var_processors.html).


---
*[Read Me](../README.md)*
