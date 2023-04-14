<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use Exception;
use Ramsey\Uuid\Uuid;

class ValueLabel extends ValueBase
{
    protected array $label_map;

    public function __construct(
        object $apiObject,
    ) {
        parent::__construct($apiObject);

        $this->label_map = array_combine(array_column($this->attribute->settings->labels, 'id'), array_column($this->attribute->settings->labels, 'name'));
    }

    public function setData(mixed $data): object
    {
        $data = array_unique($data);

        return $this->setVar('data', $data);
    }

    public function setAttribute(object $att): object
    {
        parent::setAttribute($att);
        $this->label_map = array_combine(array_column($this->attribute->settings->labels, 'id'), array_column($this->attribute->settings->labels, 'name'));

        return $this;
    }

    public function getLabelNames(?object &$att = null): array
    {
        $names = [];
        foreach ($this->data as $id) {
            $name = $this->getLabelNameFromid($id, false, $att);

            if (! is_null($name)) {
                $names[$id] = $name;
            }
        }

        return $names;
    }

    public function getLabelName($multipleError = false, ?object &$att = null): ?string
    {
        if ($multipleError === true && count($this->data) > 1) {
            throw new \Exception(count($this->data)." label values on value ($this->id) for item_id ($this->item_id) and attribute_id ($this->attribute_id)");
        }

        $name = null;
        foreach ($this->data as $id) {
            $name = $this->getLabelNameFromid($id, false, $att);

            if (! is_null($name)) {
                break;
            }
        }

        return $name;
    }

    public function getLabelNameFromId(string $id, $throwError = false, ?object &$att = null): ?string
    {
        if (is_object($att)) {
            return $att->getLabelName($id, $throwError);
        } else {
            $match = array_search($id, array_column($this->attribute->settings->labels, 'id'));

            return (is_int($match)) ? $this->attribute->settings->labels[$match]->name : null;
        }
    }

    public function removeOldLabelIds(?object &$att = null): object
    {
        foreach ($this->data as $id) {
            $name = $this->getLabelNameFromId($id, false, $att);

            if (is_null($name)) {
                $this->removeLabelId($id);
            }
        }

        return $this;
    }

    public function hasLabelName(string $name, ?object &$att = null): bool
    {
        $id = $this->getLabelId($name, false, $att);

        return ($id && $this->hasData($id)) ? true : false;
    }

    public function hasLabelId(string $id): bool
    {
        return ($this->hasData($id)) ? true : false;
    }

    public function addLabelName(string $name, ?object &$att = null): object
    {
        $id = $this->getLabelId($name, true, $att);

        return $this->addLabelId($id);
    }

    public function addLabelNames(array $names, ?object &$att = null): object
    {
        foreach ($names as $name) {
            $this->addLabelName($name, $att);
        }

        return $this;
    }

    public function removeLabelName(string $name, ?object &$att = null): object
    {
        $id = $this->getLabelId($name, false, $att);

        return (is_null($id)) ? $this : $this->removeLabelId($id);
    }

    public function removeLabelNames(array $names, ?object &$att = null): object
    {
        foreach ($names as $name) {
            $this->removeLabelName($name, $att);
        }

        return $this;
    }

    public function setLabelName(?string $name, ?object &$att = null): object
    {
        $id = (! $name) ? null : $this->getLabelId($name, true, $att);

        return $this->setLabelId($id);
    }

    public function removeLabelId(string $id): object
    {
        $val = array_values(array_diff($this->data, [$id]));

        return $this->setData($val);
    }

    public function addLabelId(string $id): object
    {
        if (! Uuid::isValid($id)) {
            throw new Exception("ID ($id) for value #$this->id for att #$this->attribute_id not a valid UUID for a label value");
        }

        $val = array_merge($this->data, [$id]);

        return $this->setData($val);
    }

    public function setLabelId(?string $id): object
    {
        return (! $id) ? $this->setData([]) : $this->setData([$id]);
    }

    public function getLabelId($name, $error = false, ?object &$att = null): ?string
    {
        if (is_object($att)) {
            return $att->getLabelId($name, $error);
        } elseif (in_array($name, $this->label_map)) {
            return array_search($name, $this->label_map);
        }

        if ($error) {
            throw new \Exception("Unable to find \$label for $name from attr #{$this->attribute->id}");
        }

        return null;
    }
}
