<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

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

    public function getLabelName($throwError = false): string
    {
        if ($throwError === true && count($this->data) > 1) {
            throw new \Exception(count($this->data)." label values on value ($this->id) for item_id ($this->item_id) and attribute_id ($this->attribute_id)");
        }

        $name = '';
        foreach ($this->data as $id) {
            $match = array_search($id, array_column($this->attribute->settings->labels, 'id'));
            if (is_int($match)) {
                $name = $this->attribute->settings->labels[$match]->name;
                break;
            }
        }

        return $name;
    }

    public function hasLabelName(string $name): bool
    {
        $id = $this->getLabelId($name, false);

        return ($id && $this->hasData($id)) ? true : false;
    }

    public function hasLabelId(string $id): bool
    {
        return ($this->hasData($id)) ? true : false;
    }

    public function addLabelName(string $name): object
    {
        $id = $this->getLabelId($name, true);

        return $this->addLabelId($id);
    }

    public function addLabelNames(array $names): object
    {
        foreach ($names as $name) {
            $this->addLabelName($name);
        }

        return $this;
    }

    public function removeLabelName(string $name): object
    {
        $id = $this->getLabelId($name, true);

        return $this->removeLabelId($id);
    }

    public function removeLabelNames(array $names): object
    {
        foreach ($names as $name) {
            $this->removeLabelName($name);
        }

        return $this;
    }

    public function setLabelName(string $name): object
    {
        $id = $this->getLabelId($name, true);

        return $this->setLabelId($id);
    }

    public function removeLabelId(string $id): object
    {
        $val = array_values(array_diff($this->data, [$id]));

        return $this->setData($val);
    }

    public function addLabelId(string $id): object
    {
        $val = array_merge($this->data, [$id]);

        return $this->setData($val);
    }

    public function setLabelId(string $id): object
    {
        return $this->setData([$id]);
    }

    public function getLabelId($name, $error = false): ?string
    {
        if (in_array($name, $this->label_map)) {
            return array_search($name, $this->label_map);
        }

        if ($error) {
            throw new \Exception("Unable to find \$label for $name from attr #{$this->attribute->id}");
        }

        return false;
    }
}
