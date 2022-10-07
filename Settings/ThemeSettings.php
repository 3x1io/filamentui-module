<?php

namespace Modules\Filamentui\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public string $theme_logo = '';
    public string $theme_dark_logo = '';
    public string $theme_sm_logo = '';
    public string $theme_dark_sm_logo = '';

    public static function group(): string
    {
        return 'theme';
    }
}
