<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Exception;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueBase;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueLabel;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueLink;

class Item extends ObjectBase
{
    public readonly float $sort_order;

    public string $nameId;

    protected string $folder_id;

    protected string $parent_id;

    protected array $values;

    public $has_atts = false;

    protected array $attributes = [];

    protected function setObjectVars(object $apiObject): void
    {
        parent::setObjectVars($apiObject);

        if (! empty($this->values) && isset($this->values[0]->attribute)) {
            $this->has_atts = true;

            foreach ($this->values as &$val) {
                $val = $this->convertInfValObj($val, $val->attribute->type);

                if (! isset($this->attributes[$val->attribute->id])) {
                    $this->attributes[] = $val->attribute;
                }
            }
        }
    }

    public function convertInfValObj(object $val, string $type): object
    {
        switch($type) {
            case 'link':
                $val = new ValueLink($val);
                break;
            case 'label':
                $val = new ValueLabel($val);
                break;
            default:
                $val = new ValueBase($val);
                break;
        }

        return $val;
    }

    public function getFolderId(): string
    {
        return $this->folder_id;
    }

    public function setFolderId($val): object
    {
        return $this->setVar('folder_id', $val);
    }

    public function getParentId(): string
    {
        return $this->parent_id;
    }

    public function setParentId($val): object
    {
        return $this->setVar('parent_id', $val);
    }

    protected function getUpdateSet()
    {
        $this->unsetEmptyVals();

        return [
            'folder_id' => $this->folder_id,
            'values' => $this->values,
            'parent_id' => $this->parent_id,
        ];
    }

    protected function getDeleteSet(): array
    {
        $set = [];
        foreach ($this->values as $val) {
            if (empty($val->data) && isset($val->id) && is_string($val->id)) {
                $set[] = $val->id;
            }
        }

        return $set;
    }

    protected function unsetEmptyVals(): void
    {
        foreach ($this->values as &$val) {
            if (empty($val->data)) {
                unset($val);
            }
        }
    }

    public function genValue(mixed $data, string $aid, ?string $type): string
    {
        $val = (object) [
            'id' => $this->generateId(),
            'object' => 'value',
            'data' => $data,
            'attribute_id' => $aid,
            'item_id' => $this->id,
            'deleted' => false,
        ];

        if (isset($this->attributes[$aid])) {
            $val->attribute = $this->attributes[$aid];
        } elseif (is_null($type)) {
            throw new Exception("Unable to find value with attribute_id ($aid) and no type provide to generate");
        }

        $this->updated = true;
        $this->values[] = $val;

        return $val->id;
    }

    public function setDataByAid(mixed $data, string $aid, ?string $type): string
    {
        $valMatch = array_search($aid, array_column($this->values, 'attribute_id'));
        if (! is_int($valMatch === false)) {
            $id = $this->genValue($data, $aid, $type);
        } else {
            $this->values[$valMatch] = $this->setValueData($this->values[$valMatch], $data);
            $id = $this->values[$valMatch]->id;
        }

        return $id;
    }

    public function getDataByAid(string $aid): mixed
    {
        $valMatch = array_search($aid, array_column($this->values, 'attribute_id'));
        if (! is_int($valMatch === false)) {
            return;
        }

        return $this->values[$valMatch]->data;
    }

    public function setValueData(object $value, mixed $data): object
    {
        if ($value->data !== $data) {
            $value->data = $data;
            $this->updated = true;
        }

        return $value;
    }
}
