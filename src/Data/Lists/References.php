<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Lists;

class References extends ListBase
{
    public function findRefsToItem(string $iid, string $aid, ?string $key = 'id')
    {
        return $this->findRefsForItem($iid, $aid, 'to_item_id', $key);
    }

    public function findRefsFromItem(string $iid, string $aid, ?string $key = 'id')
    {
        return $this->findRefsForItem($iid, $aid, 'from_item_id', $key);
    }

    public function findRefsForItem(string $iid, string $aid, string $dir, ?string $key = 'id')
    {
        $dir = ($dir === 'from_item_id') ? 'from_item_id' : 'to_item_id';
        $return = ($dir === 'from_item_id') ? 'to_item_id' : 'from_item_id';

        $list = [];
        foreach ($this->list as $ref) {
            if ($ref->$dir === $iid && $aid === $ref->attribute_id) {
                $list[] = ($key === 'id') ? $ref->$return : $ref;
            }
        }

        return ($key === 'id') ? $list : $this->collect($list);
    }
}
