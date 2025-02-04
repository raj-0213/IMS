<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function find($id);
}