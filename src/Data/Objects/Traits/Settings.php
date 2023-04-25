<?php

namespace KyleWLawrence\Infinity\Data\Objects\Traits;

trait Settings
{
    protected object $settings;

    public function getSettings(): object
    {
        return $this->settings;
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
            $this->$key = $val;
            $this->updated = true;
        }

        return $this;
    }
}
