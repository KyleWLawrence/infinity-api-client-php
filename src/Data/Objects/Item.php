<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Exception;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueBase;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueLabel;
use KyleWLawrence\Infinity\Data\Objects\ItemValue\ValueLink;

class Item extends ObjectBase
{
    public string $name_id;

    protected string $folder_id;

    protected ?string $parent_id;

    public string $object = 'item';

    protected array $values;

    public $has_atts = false;

    public array $attributes = [];

    public function getValues(): array
    {
        return $this->values;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array|object $atts): object
    {
        if (is_object($atts)) {
            $atts = $atts->toArray();
        }

        $atts = array_combine(array_column($atts, 'id'), $atts);
        $this->attributes = $atts;
        $this->has_atts = true;

        foreach ($this->values as &$val) {
            $aid = $val->attribute_id;
            if (! isset($this->attributes[$aid])) {
                throw new Exception("Unable to find Attribute by ID ($aid) in att list for item #{$val->id}");
            }

            $val->attribute = $this->attributes[$aid];
            $val = $this->convertInfValObj($val, $val->attribute->type);
        }

        return $this;
    }

    public function resetAttributes(array $atts): object
    {
        $atts = array_combine(array_column($atts, 'id'), $atts);
        $this->attributes = $atts;
        $this->has_atts = true;

        foreach ($this->values as &$val) {
            $aid = $val->attribute_id;
            if (! isset($this->attributes[$aid])) {
                throw new Exception("Unable to find Attribute by ID ($aid) in att list for item #{$val->id}");
            }

            $val->attribute = $this->attributes[$aid];
        }

        return $this;
    }

    public function convertInfValObj(object $val, string $type): object
    {
        if (get_class($val) !== 'stdClass') {
            return $val;
        }

        switch($type) {
            case 'links':
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

    public function getUpdateSet()
    {
        $this->unsetEmptyVals();

        return [
            'folder_id' => $this->folder_id,
            'values' => $this->values,
            'parent_id' => $this->parent_id,
        ];
    }

    public function getDeleteSet(): array
    {
        $set = [];
        foreach ($this->values as $val) {
            if ($val->shouldDelete()) {
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

    public function genValue(string $aid, mixed $data, ?string $type): object
    {
        $val = (object) [
            'object' => 'value',
            'data' => $data,
            'attribute_id' => $aid,
            'deleted' => false,
        ];

        if (isset($this->id)) {
            $val->item_id = $this->id;
        }

        if (isset($data) && $data !== null && ! empty($data) && $data !== false) {
            $val->id = $this->generateId();
            $this->updated = true;
        }

        if (isset($this->attributes[$aid])) {
            $val->attribute = $this->attributes[$aid];
            $type = $this->attributes[$aid]->type;
        } elseif (is_null($type)) {
            throw new Exception("Unable to find attribute ($aid) and no type provide to generate");
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

    public function setDataByAid(string $aid, bool|string|array $data, ?string $type = null): object
    {
        $valMatch = array_search($aid, array_column($this->values, 'attribute_id'));
        if (! is_int($valMatch)) {
            return $this->genValue($aid, $data, $type);
        } else {
            $this->values[$valMatch]->setData($data);
        }

        return $this;
    }

    public function getValueByAid(string $aid, ?string $type = null): object
    {
        $valMatch = array_search($aid, array_column($this->values, 'attribute_id'));
        if (! is_int($valMatch)) {
            return $this->genValue($aid, null, $type);
        }

        return $this->values[$valMatch];
    }
}
