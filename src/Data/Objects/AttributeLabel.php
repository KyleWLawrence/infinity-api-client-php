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

    public function getLabelName(string $id, $throwError = true): ?string
    {
        if (! isset($this->label_map[$id]) && $throwError) {
            throw new Exception("Unable to find \$label for $id from attr #{$this->id}");
        }

        return (isset($this->label_map[$id])) ? $this->label_map[$id] : null;
    }

    public function getLabelNames(array $ids): array
    {
        $idKeys = array_flip($ids);

        return array_intersect_key($this->label_map, $idKeys);
    }

    public function getLabelId($name, $error = false): ?string
    {
        if (in_array($name, $this->label_map)) {
            return array_search($name, $this->label_map);
        }

        if ($error) {
            throw new Exception("Unable to find \$label for $name from attr #{$this->id}");
        }

        return null;
    }

    public function getLabelIdBySet($set, $error = false): ?string
    {
        if (! $this->hasKeys($set, ['id', 'name'])) {
            throw new Exception('Missing Parameter for mandatory `id` and `name` on set: '.implode('|', $set));
        }

        if (in_array($set['id'], $this->label_map)) {
            return array_search($set['id'], $this->label_map);
        }

        $set = array_merge([
            'id' => '',
            'name' => '',
        ], $set);

        $nameList = array_column($this->settings->labels, 'name');
        $label = null;

        foreach ($nameList as $key => $name) {
            if (strpos($name, $set['id']) !== false) {
                $label = $this->settings->labels[$key]->id;
                break;
            }
        }

        if ($label === null && $error === true) {
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

        if ($id === null) {
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
            'color' => ($color === null) ? self::generateLightHex() : $color,
        ];

        $this->label_map[$label->id] = $label->name;
        $this->settings->labels[] = $label;
        $this->updated = true;

        return $label->id;
    }

    public function setLabelName(string $name, string $id): object
    {
        $labelKey = array_search($id, array_column($this->settings->labels, 'id'));

        if ($this->settings->labels[$labelKey]->name !== $name) {
            $this->settings->labels[$labelKey]->name = $name;
            $this->label_map[$id] = $name;
            $this->updated = true;
        }

        return $this;
    }

    public function renameLabel(string $newName, string $oldName, bool $error = false): object
    {
        $labelKey = array_search($oldName, array_column($this->settings->labels, 'name'));

        if (! is_int($labelKey)) {
            if ($error) {
                throw new Exception("Unable to find old Label '$oldName' to replace with '$newName' on Attribute $this->id");
            }

            return $this;
        }

        if ($this->settings->labels[$labelKey]->name !== $newName) {
            $this->settings->labels[$labelKey]->name = $newName;
            $this->label_map[$this->settings->labels[$labelKey]->id] = $newName;
            $this->updated = true;
        }

        return $this;
    }

    public function renameLabels(array $newNames, array $oldNames, bool $error = false): object
    {
        if (count($newNames) !== count($oldNames)) {
            throw new Exception('Different count for renaming labels. NewName list ('.implode(', ', $newNames).') vs OldName list ('.implode(', ', $oldNames).') on Attribute '.$this->id);
        }

        $i = 0;
        $newNames = array_values($newNames);
        $oldNames = array_values($oldNames);
        foreach ($newNames as $newName) {
            $oldName = $oldNames[$i++];
            $this->renameLabel($newName, $oldName, $error);
        }

        return $this;
    }

    public function genOrGetLabelId($name): string
    {
        $id = $this->getLabelId($name, false);

        if ($id === null) {
            $id = $this->genLabel($name);
        }

        return $id;
    }

    public function genLabels(array $names): object
    {
        foreach ($names as $name) {
            $id = $this->getLabelId($name, false);

            if ($id === null) {
                $id = $this->genLabel($name);
            }
        }

        return $this;
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

    public function removeLabelName(string $name): object
    {
        $key = array_search($name, array_column($this->settings->labels, 'name'));

        if (is_int($key)) {
            $id = $this->settings->labels[$key];
            unset($this->settings->labels[$key]);
            $this->settings->labels = array_values($this->settings->labels);
            $this->updated = true;

            if (in_array($id, $this->default_data)) {
                $this->setVar('default_data', []);
            }
        }

        return $this;
    }

    public function removeLabelId(string $id): object
    {
        $key = array_search($id, array_column($this->settings->labels, 'id'));

        if (is_int($key)) {
            unset($this->settings->labels[$key]);
            $this->settings->labels = array_values($this->settings->labels);
            $this->updated = true;

            if (in_array($id, $this->default_data)) {
                $this->setVar('default_data', []);
            }
        }

        return $this;
    }
}
