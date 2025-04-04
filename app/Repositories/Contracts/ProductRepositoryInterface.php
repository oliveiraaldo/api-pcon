<?php
namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findByName(string $name);

    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
