<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Lists;

use Exception;
use LogIt;

class Items extends ListBase
{
    public function __construct(
        array $apiObjects,
        protected ?string $board_id,
        public ?array $attributes = null,
    ) {
        if (! is_null($attributes)) {
            $this->attributes = array_combine(array_column($attributes, 'id'), $attributes);
        }

        parent::__construct($apiObjects, $board_id);
    }

    protected function setObjects($apiObjects): void
    {
        foreach ($apiObjects as $obj) {
            if (get_class($obj) === 'stdClass') {
                if (isset($obj->deleted) && $obj->deleted === true) {
                    LogIt::reportWarning("Unexpected deleted item: $obj->id");
                }

                $this->list[] = conv_inf_obj($obj, $this->board_id, $this->attributes);
            } else {
                $this->list[] = $obj;
            }
        }
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

        return (count($items) > 0) ? $items[0] : null;
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
                    if ($item->getValues()[$key]->getData() == $data) {
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
        $atts = reset($this->list)->getAttributes();
        if (! isset($atts[$aid])) {
            throw new Exception("Unable to find attribute for $aid");
        }

        $labelKey = array_search($name, array_column($atts[$aid]->settings->labels, 'name'));
        if (! is_int($labelKey)) {
            throw new Exception("Unable to find attribute label '$name' on attribute with $aid");
        }

        $id = $atts[$aid]->settings->labels[$labelKey]->id;

        return $this->findItemsByData($id, $aid);
    }

    public function getValueListsByName(array $options = []): array
    {
        $function = 'getValueListByName';

        return $this->genValueLists($function, $options);
    }

    public function getValueListsByAid(array $options = []): array
    {
        $function = 'getValueListByAid';

        return $this->genValueLists($function, $options);
    }

    protected function genValueLists(string $function, array $options): array
    {
        $list = [];

        foreach ($this->list as $item) {
            $list[$item->id] = $item->$function($options);
        }

        return $list;
    }
}
