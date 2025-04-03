<?php
namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductById(int $id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data)
    {
        $bolProductPresent = $this->bolProductPresent($data['name']);
        if($bolProductPresent){
            return response()->json( ['message' => "Produto já existe", ], 200);

        }
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id)
    {
        return $this->productRepository->delete($id);
    }

    public function bolProductPresent(string $name) {
        $product = $this->productRepository->findByName($name);
        return $product ?? false;
    }
}
