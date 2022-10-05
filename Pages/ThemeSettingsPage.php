<?php

namespace Modules\FilamentUI\Pages;

use Modules\Base\Services\Rows\Media;
use Modules\FilamentUI\Settings\ThemeSettings;
use Modules\Base\Services\Rows\Text;
use Modules\Settings\Services\Setting;


class ThemeSettingsPage extends Setting
{
    public ?string $setting = ThemeSettings::class;
    public ?bool $api = true;
    public ?string $path = "theme";
    public ?string $group = "Settings";
    public ?string $icon = "bx bxs-brush";

    public  function rows(): array
    {
        return [
            Media::make('theme_logo')->label(__('Theme Logo')),
            Media::make('theme_dark_logo')->label(__('Theme Dark Logo')),
            Media::make('theme_sm_logo')->label(__('Theme Small Logo')),
            Media::make('theme_dark_sm_logo')->label(__('Theme Small Logo')),
        ];
    }
}
