<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use Infinity;

class Attribute extends ObjectBase
{
    public string $name;

    protected object $settings;

    protected array $label_map;

    protected array $folders;

    public array $folder_ids = [];

    public array $folder_names = [];

    public array $data_type = [
        'checkbox' => 'bool',
        'created_at' => 'string',
        'created_by' => 'int',
        'data' => 'string',
        'email' => 'string',
        'label' => 'array',
        'links' => 'array',
        'longtext' => 'string',
        'members' => 'array',
        'number' => 'float,int',
        'phone' => 'string',
        'progress' => 'int',
        'rating' => 'int',
        'source_folder' => 'string',
        'text' => 'string',
        'updated_at' => 'string',
        'vote' => 'int',
    ];

    protected function getUpdateSet()
    {
        return [
            'name' => $this->name,
            'default_data' => $this->default_data,
            'settings' => $this->settings,
            'type' => $this->type,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($val): object
    {
        return $this->setVar('name', $val);
    }

    public function getSettings(): object
    {
        return $this->settings;
    }

    public function setAllSettings($val): object
    {
        return $this->setVar('settings', $val);
    }

    public function setSettings($set): object
    {
        foreach ($set as $key => $val) {
            $this->setVar($key, $val);
        }

        return $this;
    }

    //-----------------------------------------------------------------------------------
    //  Finding/Matching Item
    //-----------------------------------------------------------------------------------

    public static function get_by_id_from_list($iid, $list, $error = false)
    {
        $match = array_search($iid, array_column($list, 'id'));
        if ($error === true && $match === false) {
            LogIt::throwError("Unable to find \$match for $iid from list", $list);
        } //throw New Exception(  );

        return ($match === false) ? false : [
            'key' => $match,
            'object' => $list[$match],
        ];
    }

    public static function match_item_to_str_value($val, $items, $aid)
    {
        foreach ($items as $item) {
            if (! isset($item['values']) || $item['deleted'] === true) {
                continue;
            }

            foreach ($item['values'] as $data) {
                if ($data['data'] == $val && $data['attribute_id'] === $aid) {
                    return $item;
                }
            }
        }

        return false;
    }

    // Need to retire this and replace with above
    public static function match_attr_id_to_item_value($items, $attr, $aid, $attr_key = 'id')
    {
        if (! is_array($attr)) {
            $attr = [$attr_key => $attr];
        }

        foreach ($items as $item) {
            if (! isset($item['values']) || $item['deleted'] === true) {
                continue;
            }

            foreach ($item['values'] as $data) {
                if ($data['data'] == $attr[$attr_key] && $data['attribute_id'] === $aid) {
                    return $item;
                }
            }
        }

        return false;
    }

    //-----------------------------------------------------------------------------------
    //  Attributes
    //-----------------------------------------------------------------------------------

    public static function get_attr_by_name($name, $boardId)
    {
        $atts = Infinity::get_board_atts($boardId, true);
        $att_key = array_search($name, array_column($atts, 'name'));

        if ($att_key !== false) {
            return $atts[$att_key];
        }

        return false;
    }

    public static function match_item_by_attr_value($val, $aid, $items)
    {
        foreach ($items as $item) {
            $match = array_search($aid, array_column($item['values'], 'id'));

            if ($match !== false && $item['values'][$match]['data'] == $val) {
                return $item;
            }
        }

        return false;
    }

    public static function get_attr_from_list($atts, $id, $key = 'id')
    {
        $att_key = array_search($id, array_column($atts, $key));

        if ($att_key !== false) {
            return $atts[$att_key];
        }

        return false;
    }
}
