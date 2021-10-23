# Plans

- [x] Implement transformers:
   - Keys sorter
   - Keys cleaner
   - YAML keys transformer
- [x] Implement command for transformation of certain YAML translation file.
- [x] Implement autowiring of project translation files and command which will transform them all.
- [x] Add a wrapper for a standard translator for automatic registration of missed translations.
- [x] [Implement base testing of translations](docs/lint/lint_yaml_command.md):
    - all files presented
    - all variable filled for each language
    - files have duplicated keys
    - not transformed files detection
    - all keys of translations match pattern.
- [ ] Implement extended testing of translations:
    - empty values detector.
- [x] Make compatible with Symfony versions since 3.4 till 5.2.
- [ ] Extend support of translation files formats.
- Implement auto-translation via vendors API:
  - [x] Google Translate
  - [ ] Yandex Translate
  - [ ] and so on.
- [x] Implement command "Make my project perfect :)".


---
*[Read Me](README.md)*
