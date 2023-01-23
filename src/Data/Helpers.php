<?php

use KyleWLawrence\Infinity\Data\Exceptions\DeletedObjectException;
use KyleWLawrence\Infinity\Data\Exceptions\UnknownObjectException;
use KyleWLawrence\Infinity\Data\Objects\Attribute;
use KyleWLawrence\Infinity\Data\Objects\AttributeLabel;
use KyleWLawrence\Infinity\Data\Objects\ObjectBase;
use KyleWLawrence\Infinity\Data\Objects\Item;

if (! function_exists('conv_inf_obj')) {
    /**
     * @return Infinity\Data\Objects\ObjectBase
     */
    function conv_inf_obj(object $obj, ?string $boardId = null): object
    {
        if ($obj->deleted === true) {
            throw new DeletedObjectException("Obj ($obj->id) is deleted");
        }

        if (function_exists('config') && config('infinity-laravel.objects') === true) {
            return conv_laravel_inf_obj($obj, $boardId);
        }

        switch($obj->object) {
            case 'folderview':
                $obj = new View($obj);
                break;
            case 'reference':
            case 'hook':
            case 'folder':
            case 'comment':
            case 'board':
                $obj = new ObjectBase($obj);
                break;
            case 'item':
                $obj = new Item($obj);
                break;
            case 'attribute':
                $obj = match ($obj->type) {
                    'label' => new AttributeLabel($obj),
                    default => new Attribute($obj),
                };
                break;
            default:
                throw new UnknownObjectException("Obj ($obj->object) is not recognized");
                break;
        }

        return $obj;
    }
}
