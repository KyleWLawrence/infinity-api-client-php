<?php

namespace KyleWLawrence\Infinity\Data\Lists;

use Exception;

class Items extends ListBase
{
    public bool $has_atts = false;

    public function __construct(
        array $apiObjects,
        public readonly string $board_id,
        array|object $attributes = null,
        protected $client = new InfinityService(),
    ) {
        if (! is_null($attributes)) {
            $this->has_atts = true;
            $apiObjects = $this->assignAttributes($apiObjects, $atts);
        } elseif (isset($apiObjects->values[0]->attribute)) {
            $this->has_atts = true;
        }

        $this->setObjects($apiObjects);
    }

    public function findItemsByData(array|bool|string $data, string $aid): array
    {
        return array_filter($this->list, function ($item) use ($data, $aid) {
            $matches = array_column($item->values, 'attribute_id');
            $hasAid = array_keys($matches, $aid);

            foreach ($hasAid as $key) {
                if (is_array($item->values[$key]->data)) {
                    if (in_array($data, $item->values[$key]->data)) {
                        return true;
                    }
                } else {
                    if ($item->values[$key]->data === $data) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    public function findItemsByLabelName(string $name, string $aid): array
    {
        if ($this->has_atts === false) {
            throw new Exception(__FUNCTION__." requires the item list to has_atts to find label $name on aid ($aid)");
        }

        $atts = reset($this->list)->attributes;
        $attKey = array_search($aid, array_column($atts, 'id'));
        if (! is_int($attKey)) {
            throw new Exception("Unable to find attribute for $aid");
        }

        $labelKey = array_search($name, array_column($atts[$attKey]->settings->labels, 'id'));
        if (! is_int($labelKey)) {
            throw new Exception("Unable to find attribute label '$name' on attribute with $aid");
        }

        $id = $atts[$attKey]->settings->labels[$labelKey]->id;

        return $this->findItemsByData($id, $aid);
    }

    public function assignAttsToItems(array $apiObjects, array $atts): array
    {
        $atts = array_combine(array_column($atts, 'id'), $atts);

        foreach ($apiObjects as &$item) {
            $item->hasAtts = true;
            $item->attributes = $atts;
            $item->values = $this->assignAttsToValues($item->values, $atts);
        }

        return $apiObjects;
    }

    public function assignAttsToValues(array $values, array $atts): array
    {
        foreach ($values as &$val) {
            $aid = $val->attribute_id;
            if (! isset($atts[$aid])) {
                throw new Exception("Unable to find Attribute by ID ($aid) in att list for item #{$val->id}");
            }

            $val->attribute = $atts[$aid];
        }

        return $values;
    }
}
