<?php

use KyleWLawrence\Infinity\Data\Exceptions\DeletedObjectException;
use KyleWLawrence\Infinity\Data\Lists\Attributes;
use KyleWLawrence\Infinity\Data\Lists\Folders;
use KyleWLawrence\Infinity\Data\Lists\Items;
use KyleWLawrence\Infinity\Data\Lists\ListBase;
use KyleWLawrence\Infinity\Data\Lists\References;
use KyleWLawrence\Infinity\Data\Lists\Views;
use KyleWLawrence\Infinity\Data\Objects\Attribute;
use KyleWLawrence\Infinity\Data\Objects\AttributeLabel;
use KyleWLawrence\Infinity\Data\Objects\Item;
use KyleWLawrence\Infinity\Data\Objects\ObjectBase;

if (! function_exists('conv_inf_obj')) {
    /**
     * @return Infinity\Data\Objects\ObjectBase
     */
    function conv_inf_obj(object $obj, ?string $boardId = null, null|object|array $atts = null): object
    {
        if (is_object($atts)) {
            $atts = $atts->toArray();
        }

        if (isset($obj->deleted) && $obj->deleted === true) {
            throw new DeletedObjectException("Obj ($obj->id) is deleted");
        }

        if (function_exists('config') && config('infinity-laravel.objects') === true) {
            return conv_laravel_inf_obj($obj, $boardId, $atts);
        }

        switch($obj->object) {
            case 'folderview':
                $obj = new View($obj, $boardId);
                break;
            case 'item':
                $obj = new Item($obj, $boardId);
                if (! is_null($atts)) {
                    $obj->setAttributes($atts);
                }
                break;
            case 'attribute':
                if (! isset($obj->type)) {
                    print_r($obj);
                }
                $obj = match ($obj->type) {
                    'label' => new AttributeLabel($obj, $boardId),
                    default => new Attribute($obj, $boardId),
                };
                break;
            default:
                $obj = new ObjectBase($obj, $boardId);
                break;
        }

        return $obj;
    }

    function conv_inf_list(array $array, ?string $boardId = null, null|array|object $atts = null)
    {
        if (is_object($atts)) {
            $atts = $atts->toArray();
        }

        if (function_exists('config') && config('infinity-laravel.objects') === true) {
            return conv_laravel_inf_list($array, $boardId, $atts);
        }

        $obj = reset($array)->object;

        switch($obj) {
            case 'item':
                $list = new Items($array, $boardId, $atts);
                break;
            case 'attribute':
                $list = new Attributes($array, $boardId);
                break;
            case 'reference':
                $list = new References($array, $boardId);
                break;
            case 'view':
                $list = new Views($array, $boardId);
                break;
            case 'folder':
                $list = new Folders($array, $boardId);
                break;
            default:
                $list = new ListBase($array, $boardId);
                break;
        }

        return $list;
    }
}
