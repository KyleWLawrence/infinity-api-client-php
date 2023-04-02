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

    public function __construct(
        protected object $apiObject,
        protected ?string $board_id = null,
        protected null|object|array $attributes = null,
    ) {
        parent::__construct($apiObject, $board_id);
        $this->setAttributesOnStart($attributes);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    protected function setAttributesOnStart(null|object|array $atts): void
    {
        if (is_null($atts) && isset($this->apiObject->values[0]->attribute)) {
            $atts = [];
            foreach ($this->apiObject->values as $val) {
                if (! isset($atts[$val->attribute->id])) {
                    $atts[$val->attribute->id] = $val->attribute;
                }
            }
        } elseif (is_null($atts)) {
            return;
        }

        $this->setAttributes($atts);
    }

    public function setAttributes(array|object $atts, $new = true): object
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
                throw new Exception("Unable to find Attribute by ID ($aid) in att list for value #{$val->id}");
            }

            $val->attribute = $this->attributes[$aid];

            if ($new) {
                $val = $this->convertInfValObj($val, $val->attribute->type);
            }
        }

        return $this;
    }

    public function resetAttributes(object|array $atts): object
    {
        return $this->setAttributes($atts, false);
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
        $unset = false;
        foreach ($this->values as &$val) {
            if (empty($val->data)) {
                $unset = true;
                unset($val);
            }
        }

        if ($unset) {
            $this->values = array_values($this->values);
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

    public function getValueListByName(array $options = [])
    {
        $options['key'] = 'name';

        return $this->genValueList($options);
    }

    public function getValueListByAid(array $options = [])
    {
        $options['key'] = 'aid';

        return $this->genValueList($options);
    }

    protected function genValueList(array $options)
    {
        $options = array_merge([
            'label_names' => false,
            'include_empty' => true,
        ], $options);

        $attsUsed = [];
        $list = [];
        $aidKey = ($options['key'] === 'aid') ? true : false;

        foreach ($this->values as $val) {
            $key = ($aidKey) ? $val->attribute_id : $val->attribute->name;
            $attsUsed[] = $val->attribute_id;

            if ($options['label_names'] === true && $val->attribute->type === 'label') {
                $list[$key] = $val->getLabelNames($val->getData());
            } else {
                $list[$key] = $val->getData();
            }
        }

        if ($options['include_empty']) {
            foreach ($this->attributes as $att) {
                $key = ($aidKey) ? $att->id : $att->name;

                if (! isset($list[$key])) {
                    if ($options['label_names'] === true && $att->type === 'label') {
                        $list[$key] = (empty($att->default_data)) ? [] : $att->getLabelNames($att->default_data);
                    } else {
                        $list[$key] = $att->default_data;
                    }
                }
            }
        } else {
            $list = array_diff($list, ['', null, []]);
        }

        return $list;
    }
}
