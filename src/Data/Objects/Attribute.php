<?php

namespace KyleWLawrence\Infinity\Data\Objects;

class Attribute extends ObjectBase
{
    public string $name;

    protected object $settings;

    protected array $label_map;

    protected array $folders;

    public array $folder_ids = [];

    public array $folder_names = [];

    public array $data_type = [
        'checkbox' => 'bool',
        'created_at' => 'string',
        'created_by' => 'int',
        'data' => 'string',
        'email' => 'string',
        'label' => 'array',
        'links' => 'array',
        'longtext' => 'string',
        'members' => 'array',
        'number' => 'float,int',
        'phone' => 'string',
        'progress' => 'int',
        'rating' => 'int',
        'source_folder' => 'string',
        'text' => 'string',
        'updated_at' => 'string',
        'vote' => 'int',
    ];

    public function getUpdateSet()
    {
        return [
            'name' => $this->name,
            'default_data' => $this->default_data,
            'settings' => $this->settings,
            'type' => $this->type,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($val): object
    {
        return $this->setVar('name', $val);
    }

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

    protected function sortSpecialLabels(array $labels): array
    {
        $otherValKey = array_search('Other', array_column($labels, 'name'));
        $count = count($labels);

        if (is_int($otherValKey) && $otherValKey !== $count) {
            $set = $labels[$otherValKey];
            unset($labels[$otherValKey]);
            $labels[] = $set;
        }

        return $labels;
    }

    public function sortLabels(bool $sortSpecial = true): object
    {
        $key = 'name';
        $oldLabels = $this->settings->labels;

        usort($this->settings->labels, function ($a, $b) use ($key) {
            return (is_array($a)) ? strcmp($a[$key], $b[$key]) : strcmp($a->$key, $b->$key);
        });

        if ($sortSpecial) {
            $this->settings->labels = $this->sortSpecialLabels($this->settings->labels);
        }

        if ($this->settings->labels !== $oldLabels) {
            $this->updated = true;
        }

        return $this;
    }
}
