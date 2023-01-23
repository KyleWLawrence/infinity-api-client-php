<?php

namespace KyleWLawrence\Infinity\Data\Traits;

trait SetData
{
    public function setData(object $data): void
    {
        $valMatch = array_search($data->id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            $this->data[$valMatch] = $data;
        } else {
            $this->data[] = $data;
        }
    }
}
