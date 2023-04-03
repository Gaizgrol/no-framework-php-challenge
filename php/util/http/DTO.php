<?php

namespace Http;

abstract class DTO extends Body
{
    abstract public function rules();

    public function __construct(array $keyValues)
    {
        parent::__construct($keyValues);
        $problems = $this->validate();
        if (count($problems) > 0) {
            $response = new Response();
            $response->status(Status::BAD_REQUEST)->send($problems);
        }
    }

    public function validate()
    {
        $rules = $this->rules();

        foreach ($this->data as $field => $value) {
            if (!key_exists($field, $rules)) {
                unset($this->data[$field]);
            }
        }

        $validations = [];
        foreach ($rules as $field => $rule) {
            $value = null;
            if (key_exists($field, $this->data)) {
                $value = $this?->data[$field];
            }
            $validations[$field] = $rule($value);
        }

        $invalid = array_filter($validations, function (bool|string $value) {
            if (is_bool($value) && $value) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH);

        return $invalid;
    }
}
