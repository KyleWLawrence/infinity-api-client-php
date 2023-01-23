<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use KyleWLawrence\Infinity\Data\Traits\GetData;
use KyleWLawrence\Infinity\Data\Traits\SetData;

class ValueLink extends ValueBase
{
    use SetData;
    use GetData;

    public function genLink(string $url, string $name = '', string $favicon = ''): object
    {
        // Cosider adding function to automatically retrieve favicon
        $link = (object) [
            'id' => $this->generateId(),
            'url' => $url,
            'name' => $name,
            'favicon' => $favicon,
        ];

        $this->data[] = $link;

        return $link;
    }

    public function getLinkObjByUrl(string $url): ?object
    {
        $valMatch = array_search($url, array_column($this->data, 'url'));

        if (is_int($valMatch)) {
            return $this->data[$valMatch];
        } else {
            return;
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
            return $this->genLink($url, $name, $favicon);
        } elseif ($object->name !== $name) {
            $object->name = $name;
            $this->setData($object);

            return $object;
        }
    }
}
