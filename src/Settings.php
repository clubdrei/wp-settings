<?php
/**
 * @author Christoph Bessei
 */

namespace C3\WpSettings;

use C3\WpSettings\Api\SettingsApi;
use C3\WpSettings\Exception\InvalidSettingsTabException;
use C3\WpSettings\Tab\TabInterface;

class Settings
{
    const TAB_FILTER_HOOK = 'wp_settings_tab_filter_hook';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var SettingsApi
     */
    protected $settings_api;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var \C3\WpSettings\Tab\TabInterface[]
     */
    protected $tabs = [];

    public static function register($options = []): Settings
    {
        return new static($options);
    }

    /**
     * Get the value of a settings field
     *
     * @param string $option Settings field name
     * @param mixed $section Object which implements TabInterface or class name of a class which implements TabInterface or id of tab
     * @param mixed $default Default value if the option does not exist
     * @return string
     * @throws InvalidSettingsTabException
     */
    public static function get(string $option, $section, $default = false)
    {
        if (is_string($section)) {
            if (is_a($section, TabInterface::class, true)) {
                /** @var TabInterface $section */
                $section = new $section();
                $options = get_option($section->getId());
            } else {
                $options = get_option($section);
            }
        } elseif ($section instanceof TabInterface) {
            $options = get_option($section->getId());
        } else {
            throw new InvalidSettingsTabException("Couldn't find settings tab: " . (string)$section);
        }

        if (array_key_exists($option, $options)) {
            return $options[$option];
        }

        return $default;
    }

    public function __construct($options)
    {
        $this->id = spl_object_hash($this);
        $this->settings_api = new SettingsApi();
        $this->options = $options;

        add_action('admin_init', [$this, 'adminInit']);
        add_action('admin_menu', [$this, 'adminMenu']);
    }

    public function addTab(TabInterface $tab)
    {
        $this->tabs[get_class($tab)] = $tab;
    }

    public function adminInit()
    {
        // Each instance has it's own unique tab filter hook
        $this->tabs = apply_filters($this->getTabFilterHookName(), $this->tabs);

        $this->settings_api->set_sections($this->getSettingsSections());
        $this->settings_api->set_fields($this->getSettingsFields());

        $this->settings_api->admin_init();
    }

    public function adminMenu()
    {
        add_options_page(
            $this->options['pageTitle'],
            $this->options['menuTitle'],
            $this->options['capability'],
            $this->options['menuSlug'],
            [$this, 'pluginPage']
        );
    }

    public function getSettingsSections()
    {
        $sections = [];

        foreach ($this->tabs as $tab) {
            $sections[] = [
                'id' => $tab->getId(),
                'title' => $tab->getTitle(),
            ];
        }

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function getSettingsFields()
    {
        $fields = [];

        foreach ($this->tabs as $tab) {
            $fields[$tab->getId()] = $tab->getFields();
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTabFilterHookName()
    {
        return static::TAB_FILTER_HOOK . '_' . $this->getId();
    }

    /**
     * Try to determine the relative from WordPress root directory to WP-Settings root directory
     * @return string
     */
    public static function determineSettingsPathFromWordPressRoot(): string
    {
        $wpSettingsRootDirectory = dirname(__DIR__);
        $wpRootDirectory = ABSPATH;

        // We assume that $wpSettingsRootDirectory is a subfolder of $wpRootDirectory
        // Remove the common path of both directories and we should get the the relative path to $wpSettings from $wpRootDirectory
        return substr($wpSettingsRootDirectory, strlen($wpRootDirectory));
    }

    protected function pluginPage()
    {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }
}
