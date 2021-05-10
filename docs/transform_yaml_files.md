Transformation of YAML files
============================

The first, it is more comfortable to read alphabetical sorted lists.
The second, usage of YAML format for saving of translations gives the power fo tree-view translation key 
and let to avoid repeating of words or can be concatenated if you want. 
Such keys will be automatically concatenated by translator during the parsing of fils. 
But it takes a lot of time to maintain lits in the sorted state.

So there implemented command:

```shell
php bin/console aeliot_trans_maintain:yaml:transform INCOME_FILE_PATH OUTGOING_FILE_PATH
```
If argument `OUTGOING_FILE_PATH` omitted then `OUTGOING_FILE_PATH` become same as `INCOME_FILE_PATH`. So incoming file will be updated.

**Transformations**:
- Rearrange keys. Explode keys by dots if possible and implode keys if there are some conflicts.
- Sort keys alphabetical.

In addition, this permits avoiding branches merge conflicts.


### Extended usage

With the power of Linux shell commands ([find](https://en.wikipedia.org/wiki/Find_(Unix)), [grep](https://linuxize.com/post/how-to-use-grep-command-to-search-files-in-linux/) and so on) you can update all YAML files in the directory:
```shell
find PATH_TO_DIRECTORY -type f \( -iname \*.yml -o -iname \*.yaml \) | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
```

or only one domain
```shell
find PATH_TO_DIRECTORY -type f \( -iname \*.yml -o -iname \*.yaml \) | grep DOMAIN | sort | xargs  -I {} -t  php  bin/console aeliot_trans_maintain:yaml:transform $1{}
```
here:
- `DOMAIN` - translation domain name.
- `PATH_TO_DIRECTORY` - path to the directory.

**NOTE:** There used standard `\Symfony\Component\Yaml\Yaml` class for dumping, so it inserts single-word values without escaping.

### Examples

See [income](../examples/income.yaml) and [outgoing](../examples/outgoing.yaml) files.

---
*[Read Me](../README.md)*
