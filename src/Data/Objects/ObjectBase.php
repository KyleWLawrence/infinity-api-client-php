<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Doctrine\Inflector\CachedWordInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English;
use Doctrine\Inflector\RulesetInflector;
use Ramsey\Uuid\Uuid;

class ObjectBase
{
    public string|bool|array|null $default_data;

    public float $sort_order;

    public string $type;

    public string $id;

    public string $object;

    protected string $obj_name;

    protected string $obj_name_plural;

    public string $created_at;

    public bool $deleted = false;

    public int $created_by;

    protected string $parent_resource_id_key;

    protected string $parent_resource_id;

    protected bool $updated = false;

    //-----------------------------------------------------------------------------------
    //    General
    //-----------------------------------------------------------------------------------

    public function __construct(
        protected object $apiObject,
        public readonly string $board_id,
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

    protected function setObjectVars(object $apiObject): void
    {
        $vars = (array) $apiObject;

        foreach ($vars as $key => $var) {
            $this->$key = $var;
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    protected function setVar(string $key, $val): object
    {
        if ($this->$key !== $val) {
            $this->$key = $val;
            $this->updated = true;
        }

        return $this;
    }

    public function generateId(): string
    {
        return Uuid::uuid4();
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
        return ($this->isValidId($this->id)) ? false : true;
    }

    /**
     * Check that all parameters have been supplied
     *
     * @param  array  $params
     * @param  array  $mandatory
     * @return bool
     */
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
