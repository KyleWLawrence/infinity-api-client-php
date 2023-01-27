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
                    $this->attributes[$val->attribute->id] = $val->attribute;
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

    public function isUpdated(): bool
    {
        if ($this->updated === true) {
            return true;
        }

        foreach ($this->values as $val) {
            if ($val->isUpdated()) {
                return true;
            }
        }

        return false;
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
            if (empty($val->data) && isset($val->id) && $this->isValidId($val->id)) {
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

    public function genValue(string $aid, ?mixed $data, ?string $type): object
    {
        $val = (object) [
            'object' => 'value',
            'data' => $data,
            'attribute_id' => $aid,
            'item_id' => $this->id,
            'deleted' => false,
        ];

        if (isset($data) && $data !== null && ! empty($data) && $data !== false) {
            $val->id = $this->generateId();
            $this->updated = true;
        }

        if (isset($this->attributes[$aid])) {
            $val->attribute = $this->attributes[$aid];
            $type = $this->attributes[$aid]->type;
        } elseif (is_null($type)) {
            throw new Exception("Unable to find value with attribute_id ($aid) and no type provide to generate");
        }

        $obj = $this->convertInfValObj($val, $type);
        $this->values[] = &$obj;

        return $obj;
    }

    public function getValue(string $id): object
    {
        $valMatch = array_search($id, array_column($this->values, 'id'));
        if (! is_int($valMatch)) {
            throw new Exception("Unable to find value with id ($id)");
        }

        return $this->values[$valMatch];
    }

    public function getValueByAid(string $aid, ?string $type): object
    {
        $valMatch = array_search($aid, array_column($this->values, 'attribute_id'));
        if (! is_int($valMatch)) {
            return $this->genValue($aid, null, $type);
        }

        return $this->values[$valMatch];
    }
}
