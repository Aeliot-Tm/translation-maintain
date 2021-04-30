# Configuration

First aff all, add root node `aeliot_trans_maintain:` to your config files (see [installation](installation.md)).

Basic configuration:
```yaml
aeliot_trans_maintain:
    yaml:
        indent: 4               # Size of indents in YAML files
    insert_missed_keys: no      # Switch on/off decorator for the standard translator and define mode of inserting missed keys
```

### Accepted keys for insert_missed_keys:
- **no** - then decoration switched off.
- **end** - then missed keys will be inserted to the end of file. Mode suitable for now.
- **merge** - *[EXPERIMENTAL]* then keys will be split by dots and merged into the keys tree.  

---
*[Read Me](../README.md)*
