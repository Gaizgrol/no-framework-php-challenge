<?php

namespace DTO;

use Http\DTO;

class CreateProductDTO extends DTO
{
    public function rules()
    {
        return [
            'name' => function ($value) {
                if (!is_string($value)) return 'must be a string!';
                if (strlen(trim($value)) == 0) return 'must not be empty!';
                return true;
            },
            'price' => function ($value) {
                if (!is_int($value)) return 'must be an integer!';
                if ($value <= 0) return 'must be positive!';
                return true;
            },
            'types' => function ($value) {
                if (!is_array($value)) return 'must be an array!';
                if (count($value) == 0) return 'must not be empty!';
                if (count(array_filter($value, function ($content) {
                    return !is_int($content);
                })) > 0) return 'must contain only integers!';
                return true;
            }
        ];
    }
}
