<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects;

use Doctrine\Inflector\CachedWordInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English;
use Doctrine\Inflector\RulesetInflector;
use Exception;
use Ramsey\Uuid\Uuid;

#[\AllowDynamicProperties]
class ObjectBase
{
    protected array $required = [];

    protected array $update_vars = [];

    protected ?string $id = null;

    protected ?string $object;

    protected ?int $created_by = null;

    protected ?string $created_at = null;

    protected bool $deleted = false;

    protected string $obj_name;

    protected string $obj_name_plural;

    protected bool $api_updated = false;

    protected bool $api_created = false;

    protected bool $api_deleted = false;

    protected string $parent_resource_id_key;

    protected string $parent_resource_id;

    protected bool $updated = false;

    protected array $object_keys;

    //-----------------------------------------------------------------------------------
    //    General
    //-----------------------------------------------------------------------------------

    public function __construct(
        protected object $apiObject,
        protected ?string $board_id = null,
    ) {
        $this->setObjectVars($apiObject);

        $inflector = new Inflector(
            new CachedWordInflector(new RulesetInflector(
                English\Rules::getSingularRuleset()
            )),

            new CachedWordInflector(new RulesetInflector(
                English\Rules::getPluralRuleset()
            ))
        );

        if (! isset($this->obj_name)) {
            $this->obj_name = $inflector->singularize($this->object);
        }

        if (! isset($this->obj_name_plural)) {
            $this->obj_name_plural = $inflector->pluralize($this->object);
        }
    }

    public function getKeys()
    {
        $basic = ($this->board_id) ? ['board_id'] : [];
        $keys = array_merge($basic, $this->update_vars, $this->object_keys);

        return $keys;
    }

    public function __set($key, $value)
    {
        if (in_array($key, $this->update_vars)) {
            $this->setVar($key, $value);
        }
    }

    public function __get($key)
    {
        if (in_array($key, $this->getKeys())) {
            if (isset($this->$key)) {
                return (is_object($this->$key)) ? clone $this->$key : $this->$key;
            } else {
                return null;
            }
        }
    }

    public function __isset($key)
    {
        if (in_array($key, $this->getKeys())) {
            return isset($this->$key);
        }
    }

    public function getUpdateSet()
    {
        $set = [];
        foreach ($this->update_vars as $key) {
            if (isset($this->$key)) {
                $set[$key] = $this->$key;
            }
        }

        return $set;
    }

    public function toStdObj(): object
    {
        $set = [];
        foreach ($this->object_keys as $key) {
            $set[$key] = $this->$key;
        }

        return (object) $set;
    }

    public function toFlatObj(): object
    {
        return $this->toStdObj();
    }

    protected function resetObjectVars(object $apiObject): void
    {
        $this->apiObject = clone $apiObject;
        $this->setObjectVars($apiObject);
    }

    protected function setObjectVars(object $apiObject): void
    {
        $vars = (array) $apiObject;
        $this->object_keys = array_keys($vars);
        $check = (isset($apiObject->deleted) && $apiObject->deleted === false) ? true : false;
        $diff = array_diff($this->required, $this->object_keys);

        if ($check && ! empty($diff)) {
            throw new Exception("Missing Parameters in create of object '{$this->object}'. Parameters missing: ".implode(', ', $diff));
        }

        foreach ($vars as $key => $var) {
            $this->$key = $var;
        }
    }

    protected function setVar(string $key, $val): object
    {
        if (! isset($this->$key) || $this->$key !== $val) {
            $this->$key = $val;
            $this->updated = true;
        }

        return $this;
    }

    public function setUpdated(): object
    {
        $this->updated = true;

        return $this;
    }

    public function generateId(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function isApiUpdated(): bool
    {
        return $this->api_updated;
    }

    public function isApiCreated(): bool
    {
        return $this->api_created;
    }

    public function isApiDeleted(): bool
    {
        return $this->api_deleted;
    }

    public function isValidId(string $val): bool
    {
        return Uuid::isValid($val);
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function isNew(): bool
    {
        return (isset($this->id) && $this->isValidId($this->id)) ? false : true;
    }

    public function getVar(string $key): mixed
    {
        if (isset($this->$key)) {
            return $this->$key;
        } else {
            return null;
        }
    }

    public function hasKeys(array $params, array $mandatory): bool
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (! array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }

        return true;
    }

    public function when($condition, $callback)
    {
        if ($condition) {
            return $callback($this) ?: $this;
        }

        return $this;
    }
}
