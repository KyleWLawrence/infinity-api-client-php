<?php

namespace KyleWLawrence\Infinity\Data\Objects\ItemValue;

use Ramsey\Uuid\Uuid;

class ValueBase
{
    public readonly string $id;

    public readonly string $object;

    public readonly string $attribute_id;

    public readonly string $item_id;

    public readonly bool $deleted;

    public string $data_type = 'string';

    public string|bool|array $empty_data = '';

    public object $attribute;

    protected string|array|bool|null $data;

    protected bool $updated = false;

    public function __construct(
        protected object $apiObject,
    ) {
        $this->setObjectVars($apiObject);
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    protected function setObjectVars(object $apiObject): void
    {
        $vars = (array) $apiObject;

        foreach ($vars as $key => $var) {
            $this->$key = $var;
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
        if (is_array($data)) {
            return (count(array_diff($data, $this->data)) > 0) ? false : true;
        } else {
            return ($data === $this->data) ? true : false;
        }
    }

    public function deleteData(): object
    {
        $this->setData($this->empty_data);

        return $this;
    }
}
