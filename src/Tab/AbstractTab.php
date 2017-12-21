<?php
namespace C3\WpSettings\Tab;

use C3\WpSettings\Settings;

/**
 * @author Christoph Bessei
 */
abstract class AbstractTab implements TabInterface
{
    public static function get(string $option, $default = false)
    {
        return Settings::get($option, static::class, $default);
    }
}
