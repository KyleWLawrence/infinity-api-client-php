<?php

namespace KyleWLawrence\Infinity\Data\Lists;

use Exception;

class Items extends ListBase
{
    public function __construct(
        array $apiObjects,
        protected string $board_id,
        public ?array $attributes = null,
    ) {
        if (is_null($attributes)) {
            throw new Exception('Attributes required to generate Items list');
        }

        $this->attributes = array_combine(array_column($attributes, 'id'), $attributes);

        parent::__construct($apiObjects, $board_id);

        $this->assignAttributes();
    }

    public function collect(?array $data = null)
    {
        $data = (is_null($data)) ? $this->list : $data;
        $class = get_class();
        $bid = (isset($this->board_id)) ? $this->board_id : null;
        $atts = (isset($this->attributes)) ? $this->attributes : null;

        return new $class($data, $bid, $atts);
    }

    public function assignAttributes(): object
    {
        foreach ($this->list as &$item) {
            $item->setAttributes($this->attributes);
        }

        return $this;
    }

    public function findItemByData(array|bool|string $data, string $aid): ?object
    {
        $items = $this->findItemsByData($data, $aid);
        if (count($items) > 1) {
            throw new Exception('Found '.count($items).' items when expected to find one for data '.print_r($data, true).' and \$aid '.$aid);
        }

        return (count($items) > 0) ? reset($items) : null;
    }

    public function findItemsByData(array|bool|string $data, string $aid): object
    {
        $list = array_filter($this->list, function ($item) use ($data, $aid) {
            $matches = array_column($item->getValues(), 'attribute_id');
            $hasAid = array_keys($matches, $aid);

            foreach ($hasAid as $key) {
                if (is_array($item->getValues()[$key]->getData())) {
                    if (in_array($data, $item->getValues()[$key]->getData())) {
                        return true;
                    }
                } else {
                    if ($item->getValues()[$key]->getData() === $data) {
                        return true;
                    }
                }
            }

            return false;
        });

        return $this->collect($list);
    }

    public function findItemsByLabelName(string $name, string $aid): object
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
}
