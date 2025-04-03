<?php

use App\Models\Product;
use App\Services\ProductService;
use App\Repositories\EloquentProductRepository;

// Configuração inicial
beforeEach(function () {
    $this->repository = new EloquentProductRepository(new Product());
    $this->service = new ProductService($this->repository);
});

// Testes do Model
it('has correct fillable attributes', function () {
    $fillable = ['name', 'description', 'price', 'quantity'];
    expect((new Product())->getFillable())->toBe($fillable);
});

// Testes do Repository
it('repository can find a product', function () {
    $product = Product::factory()->create();

    $found = $this->repository->find($product->id);

    expect($found)
        ->toBeInstanceOf(Product::class)
        ->and($found->id)->toBe($product->id);
});

it('repository returns null when product not found', function () {
    expect($this->repository->find(999))->toBeNull();
});

// Testes do Service
it('service can create a product', function () {
    $productData = [
        'name' => 'Service Test',
        'price' => 150.99,
        'quantity' => 5
    ];

    $product = $this->service->createProduct($productData);

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Service Test')
        ->and($product->exists)->toBeTrue();
});

it('service validates price increase limit', function () {
    $product = Product::factory()->create(['price' => 100]);

    // Testa aumento de 20% (deve passar)
    $updated = $this->service->updateProductPrice($product->id, 120);
    expect($updated->price)->toBe(120.0);

    // Testa aumento de 21% (deve falhar)
    expect(fn() => $this->service->updateProductPrice($product->id, 121))
        ->toThrow(Exception::class, 'Aumento máximo de 20% permitido');
});

// Testes da API
it('can list products via API', function () {
    Product::factory()->count(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertOk()
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'price', 'quantity']
        ]);
});

it('can create product via API', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'API Test',
        'price' => 99.99,
        'quantity' => 10
    ]);

    $response->assertCreated()
        ->assertJson(['name' => 'API Test']);

    $this->assertDatabaseHas('products', ['name' => 'API Test']);
});

it('validates product creation via API', function () {
    $response = $this->postJson('/api/products', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'price', 'quantity']);
});

it('can update product via API', function () {
    $product = Product::factory()->create(['price' => 50]);

    $response = $this->putJson("/api/products/{$product->id}", [
        'price' => 60
    ]);

    $response->assertOk()
        ->assertJson(['price' => 60]);

    expect($product->fresh()->price)->toBe(60.0);
});

it('can delete product via API', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
