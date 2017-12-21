<?php
namespace C3\WpSettings\Tab;

use C3\WpSettings\Settings;

/**
 * @author Christoph Bessei
 */
abstract class AbstractTab implements TabInterface
{
    /**
     * @param string $option
     * @param bool $default
     * @return string
     */
    public static function get(string $option, $default = false)
    {
        return Settings::get($option, static::class, $default);
    }

    /**
     * @param string $option
     * @param $value
     * @throws \C3\WpSettings\Exception\InvalidSettingsTabException
     */
    public static function update(string $option, $value)
    {
        Settings::update($option, static::class, $value);
    }
}
