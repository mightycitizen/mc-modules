# MC Icons
This module is a fork of the [ui_icons_examples](https://gitlab.com/ui-icons/ui-icons-example) repository and contains just the `ui_phosphor_icons` folder for integration with [Phosphor Icons](https://phosphoricons.com). This module can be

Includes:
* Icons from the Phosphor icon set (see Installation & Updates).
* A custom FieldType Plugin for UI Icon field integration with GraphQL and GraphQL Compose.
* Hooks to create and extend schema types for Icon + Link integration with GraphQL Compose.

## Installation & Updates
The [Phosphor Icons](https://github.com/phosphor-icons/core) set is already included in this module (see icons/phosphor), but the icon set can be installed or updated via npm:

```shell
npm i @phosphor-icons/core
cp -R node_modules/@phosphor-icons/core/assets/* icons/
```
