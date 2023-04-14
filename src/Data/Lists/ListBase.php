<?php

namespace KyleWLawrence\Infinity\Data\Lists;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use LogIt;
use Traversable;

#[AllowDynamicProperties]
class ListBase implements ArrayAccess, IteratorAggregate, Countable
{
    protected array $list = [];

    protected ?string $name_key = null;

    public function __construct(
        array $apiObjects,
        protected ?string $board_id = null,
    ) {
        $this->setObjects($apiObjects);
    }

    public function all(): array
    {
        return $this->list;
    }

    public function toArray(): array
    {
        $list = [];

        foreach ($this->list as $obj) {
            $list[] = $obj->toStdObj();
        }

        return $list;
    }

    public function count(): int
    {
        return count($this->list);
    }

    protected function setObjects($apiObjects): void
    {
        foreach ($apiObjects as $obj) {
            if (isset($obj->deleted) && $obj->deleted === true) {
                LogIt::reportWarning("Unexpected deleted item: $obj->id");
            }

            if (get_class($obj) === 'stdClass') {
                $this->list[] = conv_inf_obj($obj, $this->board_id);
            } else {
                $this->list[] = $obj;
            }
        }
    }

    public function each(callable $callback)
    {
        foreach ($this->list as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    public function collect(?array $data = null)
    {
        $data = (is_null($data)) ? $this->list : $data;
        $class = get_class();
        $bid = (isset($this->board_id)) ? $this->board_id : null;

        return new $class($data, $bid);
    }

    public function getColumn($column)
    {
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

    public function getByName(string $search, bool $full = true): ?object
    {
        return $this->getByKey($search, 'name', $full);
    }

    public function getByKey(string $search, string $key, bool $full = true): ?object
    {
        if ($full) {
            $itemKey = array_search($search, $this->getColumn($key));
        } else {
            $itemKey = false;
            foreach ($this->list as $ikey => &$item) {
                if ($full && strpos($item->$key, $search) !== false) {
                    $itemKey = $ikey;
                    break;
                } elseif ($item->$key === $search) {
                    $itemKey = $ikey;
                    break;
                }
            }
        }

        if (! is_int($itemKey)) {
            return null;
        }

        return $this->list[$itemKey];
    }

    public function findByName(string $search, bool $full = true): ?object
    {
        return $this->findByKey($search, 'name', $full);
    }

    public function findByKey(string $search, string $key, bool $full = true): ?object
    {
        $items = [];
        foreach ($this->list as &$item) {
            if ($full) {
                if ($item->$key === $search) {
                    $items[] = $item;
                }
            } else {
                if (strpos($item->$key, $search) !== false) {
                    $items[] = $item;
                }
            }
        }

        return $this->collect($items);
    }

    public function getDupesByKey(string $key): object
    {
        $dupes = [];
        $vals = [];

        foreach ($this->list as &$item) {
            $val = $item->getByKey($key);

            if (in_array($val, $vals)) {
                $dupes[] = $item;
            } elseif (! empty($val)) {
                $vals[] = $val;
            }
        }

        return $this->collect($dupes);
    }

    public function list(): array
    {
        if ($this->name_key === null) {
            $list = $this->getColumn('id');
        } else {
            $keys = $this->getColumn('name');
            $ids = $this->getColumn('id');
            $list = array_combine($keys, $ids);
        }

        return $list;
    }

    public function add(object $item): object
    {
        $itemKey = array_search($item->id, $this->getColumn('id'));
        if (! is_int($itemKey)) {
            $this->list[] = $item;
        } else {
            $this->list[$itemKey] = $item;
        }

        return $this;
    }

    public function combine(array $list): object
    {
        $this->list = array_merge($this->list, $list);

        return $this;
    }

    public function remove($key): object
    {
        unset($this->list[$key]);

        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
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
}
