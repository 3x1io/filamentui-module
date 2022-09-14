# Filament UI Theme for VILT

Filament PHP UI kit for VILT stack

## Install

```bash
composer require 3x1io/filamentui-module
```
Add Module to `modules_statuses.json` if not exists

```json
{
    "FilamentUI": true
}
```

```dotenv
THEME_MODULE=FilamentUI
```

```bash
php artisan config:clear
```

Publish Assets

```bash
yarn
```

```bash
yarn add tippy.js
```

```bash
yarn build
```

## Support

you can join our discord server to get support [VILT Admin](https://discord.gg/HUNYbgKDdx)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Fady Mondy](https://github.com/3x1io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

