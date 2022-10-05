<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class ThemeSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('theme.theme_logo', '');
        $this->migrator->add('theme.theme_dark_logo', '');
        $this->migrator->add('theme.theme_sm_logo', '');
        $this->migrator->add('theme.theme_dark_sm_logo', '');
    }
}
