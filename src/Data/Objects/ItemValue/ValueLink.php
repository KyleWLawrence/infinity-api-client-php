<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use Exception;

class ValueLink extends ValueBase
{
    public string|bool|array $empty_data = [];

    public function removeLinkById(string $id): object
    {
        $valMatch = array_search($id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            unset($this->data[$valMatch]);

            return $this;
        } else {
            throw new Exception("Unable to find link \$value for $id from item #{$this->item_id}");
        }
    }

    public function removeLinkByUrl(string $url): object
    {
        $valMatch = array_search($url, array_column($this->data, 'url'));

        if (is_int($valMatch)) {
            unset($this->data[$valMatch]);
        }

        return $this;
    }

    public function deleteData(): object
    {
        return $this->setData([]);
    }

    public function getLinkById(string $id): object
    {
        $valMatch = array_search($id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            return $this->data[$valMatch];
        } else {
            throw new Exception("Unable to find link \$value for $id from item #{$this->item_id}");
        }
    }

    public function setLink(mixed $data): object
    {
        $valMatch = array_search($data->id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            if ($this->data[$valMatch] !== $data) {
                $this->updated = true;
                $this->data[$valMatch] = $data;
            }
        } else {
            $this->updated = true;
            $this->data[] = $data;
        }

        return $this;
    }

    public function genLink(string $url, string $name = '', string $favicon = ''): object
    {
        // Cosider adding function to automatically retrieve favicon
        $link = (object) [
            'id' => $this->generateId(),
            'url' => $url,
            'name' => $name,
            'favicon' => $favicon,
        ];

        $this->updated = true;
        $this->data[] = $link;

        return $link;
    }

    public function getLinkObjByUrl(string $url): ?object
    {
        $valMatch = array_search($url, array_column($this->data, 'url'));

        if (is_int($valMatch)) {
            return $this->data[$valMatch];
        }
    }

    public function getLinkNameByUrl(string $url): ?string
    {
        $object = $this->getLinkObjByUrl($url);

        return ($object) ? $object->name : null;
    }

    public function getLinkIdByUrl(string $url): ?string
    {
        $object = $this->getLinkObjByUrl($url);

        return ($object) ? $object->id : null;
    }

    public function getOrAddLink(string $url, string $name = '', string $favicon = ''): object
    {
        $object = $this->getLinkObjByUrl($url);

        if (! is_object($object)) {
            return $this->genLink($url, $name, $favicon);
        } else {
            return $object;
        }
    }

    public function updateOrAddLink(string $url, string $name = '', string $favicon = ''): object
    {
        $object = $this->getLinkObjByUrl($url);

        if (! is_object($object)) {
            $this->genLink($url, $name, $favicon);
        } elseif ($object->name !== $name) {
            $object->name = $name;
            $this->setLink($object);
        }

        return $this;
    }
}
