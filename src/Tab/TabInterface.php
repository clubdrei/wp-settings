<?php
/**
 * @author Christoph Bessei
 */
namespace C3\WpSettings\Tab;

interface TabInterface
{
    public function getId(): string;

    public function getTitle(): string;

    public function getFields(): array;

    public static function get(string $option, $default = false);

    public static function update(string $option, $value);
}