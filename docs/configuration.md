Configuration
=============

First aff all, add root node `aeliot_trans_maintain:` to your config files (see [installation](installation.md)).

### Basic configuration:

```yaml
aeliot_trans_maintain:
    yaml:
        indent: 4               # Size of indents in YAML files
    insert_missed_keys: no      # Switch on/off decorator for the standard translator and define mode of inserting missed keys
```

#### Accepted keys for insert_missed_keys:

- **no** - then decoration switched off.
- **end** - then missed keys will be inserted to the end of file. Mode suitable for now.
- **merge** - *[EXPERIMENTAL]* then keys will be split by dots and merged into the keys tree.

It is recommended to use values: "no" or "end".

### Usage of Environment variable:

Example (you can get more information in the [document](https://symfony.com/doc/current/configuration/env_var_processors.html)):

```yaml
# Add parameter TRANS_MAINTAIN_INSERT_MISSED_KEYS=end into .env.local to switch on translator decorator and clear cache
parameters:
    env(TRANS_MAINTAIN_INSERT_MISSED_KEYS): no

aeliot_trans_maintain:
    insert_missed_keys: "%env(TRANS_MAINTAIN_INSERT_MISSED_KEYS)%"
```

After that you can easily switch on/off translator decorator and inserting of missed translation keys by adding/changing of parameter 
`TRANS_MAINTAIN_INSERT_MISSED_KEYS=end` in .env.local file in the project folder.

---
*[Read Me](../README.md)*
