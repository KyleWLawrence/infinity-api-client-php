<?php

namespace KyleWLawrence\Infinity\Data\Lists;

use Exception;

class ListBase implements \ArrayAccess
{
    protected array $list = [];

    public function __construct(
        array $apiObjects,
        protected ?string $board_id = null,
    ) {
        $this->setObjects($apiObjects);
    }

    public function toArray(): array
    {
        $list = [];

        foreach ($this->list as $obj) {
            $list[] = $obj->toStdObj();
        }

        return $list;
    }

    protected function setObjects($apiObjects): void
    {
        foreach ($apiObjects as $obj) {
            if ($obj->deleted === true) {
                throw new Exception("Unexpected deleted item: $obj->id");
            }

            $this->list[] = conv_inf_obj($obj, $this->board_id);
        }
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    public function getColumn($column) {
        return array_column($this->list, $column);
    }

    public function getById(string $id): object
    {
        $itemKey = array_search($id, $this->getColumn('id'));
        if (! is_int($itemKey)) {
            throw new Exception("Unable to find item id ($id) in list");
        }

        return $this->list[$itemKey];
    }

    public function getByKey(string $search, string $key): ?object
    {
        $itemKey = array_search($search, $this->getColumn($key));

        if (! is_int($itemKey)) {
            return null;
        }

        return $this->list[$itemKey];
    }
}
