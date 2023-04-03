<?php

use DB\Entity;

class Product extends Entity
{
    public ?int $id;
    public string $name;
    public int $price;
}
