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

    protected object $attribute;

    protected string|array|bool $data;

    public function __construct(
        protected object $apiObject,
    ): void {
        $this->setObjectVars($apiObject);
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

    public function setData($data)
    {
        $this->data = $data;
    }
}
