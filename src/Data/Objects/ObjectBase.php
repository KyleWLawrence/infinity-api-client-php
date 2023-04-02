<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Doctrine\Inflector\CachedWordInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English;
use Doctrine\Inflector\RulesetInflector;
use Ramsey\Uuid\Uuid;

#[\AllowDynamicProperties]
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

    protected bool $api_updated = false;

    protected bool $api_created = false;

    public bool $api_deleted = false;

    public ?int $created_by;

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

    public function toStdObj(): object
    {
        $set = [];
        foreach ($this->object_keys as $key) {
            $set[$key] = $this->$key;
        }

        return (object) $set;
    }

    protected function setObjectVars(object $apiObject): void
    {
        $vars = (array) $apiObject;
        $this->object_keys = array_keys($vars);

        foreach ($vars as $key => $var) {
            $this->$key = $var;
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

     public function getBoardId(): ?string
     {
         return $this->board_id;
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

    public function isApiUpdated(): bool
    {
        return $this->api_updated;
    }

    public function isApiCreated(): bool
    {
        return $this->api_created;
    }

    public function isApiDelete(): bool
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

    /**
     * Check that all parameters have been supplied
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
