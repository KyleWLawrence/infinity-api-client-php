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
use KyleWLawrence\Infinity\Data\Objects\Board;
use KyleWLawrence\Infinity\Data\Objects\Comment;
use KyleWLawrence\Infinity\Data\Objects\Folder;
use KyleWLawrence\Infinity\Data\Objects\Hook;
use KyleWLawrence\Infinity\Data\Objects\Item;
use KyleWLawrence\Infinity\Data\Objects\Reference;
use KyleWLawrence\Infinity\Data\Objects\View;
use KyleWLawrence\Infinity\Data\Objects\Workspace;

if (! function_exists('conv_inf_obj')) {
    /**
     * @return Infinity\Data\Objects\ObjectBase
     */
    function conv_inf_obj(object $obj, ?string $boardId = null, null|object|array $atts = null): object
    {
        if (isset($obj->deleted) && $obj->deleted === true) {
            throw new DeletedObjectException("Obj ({$obj->id}) is deleted");
        }

        if (function_exists('conv_laravel_inf_list')) {
            return conv_laravel_inf_obj($obj, $boardId, $atts);
        }

        $obj = match ($obj->object) {
            'attribute' => match ($obj->type) {
                'label' => new AttributeLabel($obj, $boardId),
                default => new Attribute($obj, $boardId),
            },
            'board' => new Board($obj),
            'comment' => new Comment($obj, $boardId),
            'folder' => new Folder($obj, $boardId),
            'hook' => new Hook($obj, $boardId),
            'item' => new Item($obj, $boardId, $atts),
            'reference' => new Reference($obj, $boardId),
            'folderview', 'view' => new View($obj, $boardId),
            'workspace' => new Workspace($obj),
            default => throw new Exception("Unknown Object Type: $obj->type for {$obj->id}"),
        };

        return $obj;
    }

    function conv_inf_list(array $array, string $type, ?string $boardId = null, null|array|object $atts = null)
    {
        if (function_exists('conv_laravel_inf_list')) {
            return conv_laravel_inf_list($array, $type, $boardId, $atts);
        }

        $list = match ($type) {
            'attribute', 'attributes' => new Attributes($array, $boardId),
            'item', 'items' => new Items($array, $boardId, $atts),
            'reference', 'references' => new References($array, $boardId),
            'view', 'views' => new Views($array, $boardId),
            'folder', 'folders' => new Folders($array, $boardId),
            default => new ListBase($array, $boardId),
        };

        return $list;
    }
}
