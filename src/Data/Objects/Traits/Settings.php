<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects\Traits;

trait Settings
{
    protected object $settings;

    public function getSettings(): object
    {
        $settings = clone $this->settings;

        if (isset($this->settings->attributes) && ! empty($this->settings->attributes) && is_object($this->settings->attributes[0])) {
            $atts = [];
            foreach ($this->settings->attributes as $key => $att) {
                $atts[$key] = clone $att;
            }

            $settings->attributes = $atts;
        }

        return $settings;
    }

    public function setAllSettings($val): object
    {
        return $this->setVar('settings', $val);
    }

    public function setSettings($set): object
    {
        foreach ($set as $key => $val) {
            $this->setSetting($key, $val);
        }

        return $this;
    }

    public function setSetting(string $key, $val): object
    {
        if ($this->settings->$key !== $val) {
            $this->settings->$key = $val;
            $this->updated = true;
        }

        return $this;
    }
}
