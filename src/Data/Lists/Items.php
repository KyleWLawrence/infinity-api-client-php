<?php

namespace KyleWLawrence\Infinity\Data\Lists;

use Exception;
use KyleWLawrence\Infinity\Services\InfinityService;

class Items extends ListBase
{
    public bool $has_atts = false;

    public function __construct(
        array $apiObjects,
        string $board_id,
        ?array $attributes,
        protected $client = new InfinityService(),
    ) {
        if (! is_null($attributes)) {
            $this->has_atts = true;
            $apiObjects = $this->assignAttributes($apiObjects, $attributes);
        } elseif (isset($apiObjects[0]->getValues()[0]->attribute)) {
            $this->has_atts = true;
        }

        parent::__construct($apiObjects, $board_id);
    }

    public function findItemByData(array|bool|string $data, string $aid): object
    {
        $items = $this->findItemsByData($data, $aid);
        if (count($items) > 1) {
            throw new Exception('Found '.count($items).' items when expected to find one for data '.print_r($data, true).' and \$aid '.$aid);
        }

        return reset($items);
    }

    public function findItemsByData(array|bool|string $data, string $aid): array
    {
        return array_filter($this->list, function ($item) use ($data, $aid) {
            $matches = array_column($item->getValues(), 'attribute_id');
            $hasAid = array_keys($matches, $aid);

            foreach ($hasAid as $key) {
                if (is_array($item->getValues()[$key]->data)) {
                    if (in_array($data, $item->getValues()[$key]->data)) {
                        return true;
                    }
                } else {
                    if ($item->getValues()[$key]->data === $data) {
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

        $atts = reset($this->list)->getAttributes();
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
            $item->setAttsToValues();
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
