<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use Ramsey\Uuid\Uuid;

class ValueBase
{
    public string $id;

    public string $object;

    public string $attribute_id;

    public string $item_id;

    public bool $deleted;

    public string $data_type = 'string';

    public object $attribute;

    protected string|array|bool|null $data;

    protected bool $updated = false;

    protected bool $new = false;

    public function __construct(
        object $apiObject,
    ) {
        if (! isset($apiObject->attribute) || ! is_object($apiObject->attribute)) {
            throw new \Exception('Values must be populated with an attribute');
        }

        $this->setObjectVars($apiObject);
    }

    public function getUpdateSet(): array
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'data' => $this->data,
        ];
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function isNew(): bool
    {
        return $this->new;
    }

    public function shouldDelete(): bool
    {
        if (empty($this->data) && ! $this->isNew()) {
            return true;
        }

        return false;
    }

    protected function setObjectVars(object $apiObject): void
    {
        $vars = (array) $apiObject;

        foreach ($vars as $key => $var) {
            $this->$key = $var;
        }

        if (! isset($this->data)) {
            $this->data = $this->attribute->default_data;
        }

        $this->data_type = gettype($this->attribute->default_data);

        if (! isset($this->id)) {
            $this->new = true;
            $this->id = $this->generateId();
        }
    }

    public function generateId(): string
    {
        return Uuid::uuid4();
    }

    public function getData()
    {
        return $this->data;
    }

    protected function setVar(string $key, $val): object
    {
        if ($this->$key !== $val) {
            $this->$key = $val;
            $this->updated = true;
        }

        return $this;
    }

    public function setData(mixed $data): object
    {
        return $this->setVar('data', $data);
    }

    public function hasData(mixed $data): bool
    {
        if (is_array($this->data)) {
            if (! is_array($data)) {
                $data = [$data];
            }

            return (count(array_diff($data, $this->data)) === 0) ? true : false;
        } else {
            return ($data === $this->data) ? true : false;
        }
    }

    public function deleteData(): object
    {
        $this->setData($this->attribute->default_data);

        return $this;
    }
}
