<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use Exception;

class ValueLink extends ValueBase
{
    public function removeLinkById(string $id): object
    {
        $valMatch = array_search($id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            unset($this->data[$valMatch]);
            $this->data = array_values($this->data);
            $this->updated = true;

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
            $this->data = array_values($this->data);
            $this->updated = true;
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

    public function updateOrAddLink(string $url, string $name = '', string $favicon = ''): object
    {
        $object = $this->getLinkObjByUrl($url);

        if (! is_object($object)) {
            $this->genLink($url, $name, $favicon);
        } else {
            $name = (empty($name) && isset($object->name)) ? $object->name : $name;
            $favicon = (empty($favicon) && isset($object->favicon)) ? $object->favicon : $favicon;

            if ($object->name !== $name || $object->favicon !== $favicon) {
                $link = (object) [
                    'name' => $name,
                    'favicon' => $favicon,
                    'id' => $object->id,
                    'url' => $url,
                ];
                $this->setLink($link);
            }
        }

        return $this;
    }

    public function updateOrAddLinks(array $links): object
    {
        foreach ($links as $link) {
            if (is_object($link)) {
                $link = (array) $link;
            }
            $link = array_merge(['url' => '', 'name' => '', 'favicon' => ''], $link);
            $this->updateOrAddLink($link['url'], $link['name'], $link['favicon']);
        }

        return $this;
    }

    public function replaceLinks(array $links): object
    {
        foreach ($this->data as $link) {
            $valMatch = array_search($link->url, array_column($links, 'url'));

            if (! is_int($valMatch)) {
                $this->removeLinkById($link->id);
            }
        }

        $this->updateOrAddLinks($links);

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

        return null;
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
}
