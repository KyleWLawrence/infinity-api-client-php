<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

class ValueLabel extends ValueBase
{
    public function setData(mixed $data): object
    {
        $data = array_unique($data);

        return $this->setVar('data', $data);
    }

    public function getLabelNames(): array
    {
        $names = [];
        foreach ($this->data as $id) {
            $match = array_search($id, array_column($this->attribute->settings->labels, 'id'));
            if (is_int($match)) {
                $names[$id] = $this->attribute->settings->labels[$match]->name;
            }
        }

        return $names;
    }

    public function hasLabelName(string $name, object $att): bool
    {
        $id = $att->getLabelId($name, false);

        return ($id && $this->hasData($id)) ? true : false;
    }

    public function hasLabelId(string $id): bool
    {
        return ($this->hasData($id)) ? true : false;
    }

    public function addLabelName(string $name, object $att): object
    {
        $id = $att->getLabelId($name, true);

        return $this->addLabelId($id);
    }

    public function addLabelNames(array $names, object $att): object
    {
        foreach ($names as $name) {
            $this->addLabelName($name, $att);
        }

        return $this;
    }

    public function removeLabelName(string $name, object $att): object
    {
        $id = $att->getLabelId($name, true);

        return $this->removeLabelId($id);
    }

    public function removeLabelNames(array $names, object $att): object
    {
        foreach ($names as $name) {
            $this->removeLabelName($name, $att);
        }

        return $this;
    }

    public function setLabelName(string $name, object $att): object
    {
        $id = $att->getLabelId($name, true);

        return $this->setLabelId($id);
    }

    public function removeLabelId(string $id): object
    {
        $val = array_diff($this->data, [$id]);

        return $this->setData($val);
    }

    public function addLabelId(string $id): object
    {
        $val = array_merge($this->data, $id);

        return $this->setData($val);
    }

    public function setLabelId(string $id): object
    {
        return $this->setData([$id]);
    }
}
