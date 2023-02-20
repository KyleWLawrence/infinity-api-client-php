<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Exception;

class AttributeLabel extends Attribute
{
    protected array $label_map;

    public function __construct(
        protected object $apiObject,
        protected ?string $board_id,
    ) {
        parent::__construct($apiObject, $board_id);

        $this->label_map = array_combine(array_column($this->settings->labels, 'id'), array_column($this->settings->labels, 'name'));
    }

    public function getLabelName($id): string
    {
        if (! isset($this->label_map[$id])) {
            throw new Exception("Unable to find \$label for $id from attr #{$this->id}");
        }

        return $this->label_map[$id];
    }

    public function getLabelId($name, $error = false): ?string
    {
        if (in_array($name, $this->label_map)) {
            return array_search($name, $this->label_map);
        }

        if ($error) {
            throw new Exception("Unable to find \$label for $name from attr #{$this->id}");
        }

        return false;
    }

    public function getLabelIdBySet($set, $error = false): string|bool
    {
        if (! $this->hasKeys($set, ['id', 'name'])) {
            throw new Exception('Missing Parameter for mandatory `id` and `name` on set: '.implode('|', $set));
        }

        if (in_array($set->id, $this->label_map)) {
            return array_search($set->id, $this->label_map);
        }

        $set = array_merge([
            'id' => '',
            'name' => '',
        ], $set);

        $nameList = array_column($this->settings->labels, 'name');
        $label = false;

        foreach ($nameList as $key => $name) {
            if (strpos($name, $set['id']) !== false) {
                $label = $this->settings->labels[$key]->id;
                break;
            }
        }

        if ($label === false && $error === false) {
            throw new Exception("Unable to find \$label for {$set['name']} ({$set['id']}) from attr #{$this->id}");
        }

        return $label;
    }

    public function genOrGetLabelIdBySet($set): string
    {
        if (! $this->hasKeys($set, ['id', 'name'])) {
            throw new Exception('Missing Parameter for mandatory `id` and `name` on set: '.implode('|', $set));
        }

        $name = "{$set['name']} ({$set['id']})";
        $id = $this->getLabelIdBySet($set, false);

        if ($id === false) {
            $id = $this->genLabel($name);
        } elseif ($this->getLabelName($id) !== $name) {
            $this->setLabelName($name, $id);
        }

        return $id;
    }

    public function genLabel($name, $color = null): string
    {
        $label = (object) [
            'name' => $name,
            'id' => $this->generateId(),
            'color' => ($color === null) ? $this->generateLightHex() : $color,
        ];

        $this->label_map[$label->id] = $label->name;
        $this->settings->labels[] = $label;
        $this->updated = true;

        return $label->id;
    }

    public function setLabelName($name, $id): void
    {
        $labelKey = array_search($id, array_column($this->settings->labels, 'id'));

        if ($this->settings->labels[$labelKey]->name !== $name) {
            $this->settings->labels[$labelKey]->name = $name;
            $this->label_map[$id] = $name;
            $this->updated = true;
        }
    }

    public function genOrGetLabelId($name): string
    {
        $id = $this->getLabelId($name);

        if ($name === false) {
            $id = $this->genLabel($name);
        }

        return $id;
    }

    public function getDefaultName($name = false): string
    {
        if (! empty($this->default_data)) {
            $id = reset($this->default_data);

            return $this->getLabelName($id);
        } elseif (is_string($name)) {
            $this->genOrGetLabelId($name);
        }

        throw new Exception("Unable to find `default_data` on attr #{$this->id}");
    }

    public static function generateLightHex(): string
    {
        $dt = '';
        for ($o = 1; $o <= 3; $o++) {
            $dt .= str_pad(dechex(mt_rand(128, 256)), 2, '0', STR_PAD_LEFT);
        }

        return "#{$dt}";
    }
}
