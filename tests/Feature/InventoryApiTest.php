<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_and_read_products()
    {
        // 1. Create product
        $response = $this->postJson('/api/products', [
            'name' => 'Test Gaming Mouse',
            'sku' => 'MOUSE-TEST-001',
            'description' => 'A mouse for testing features',
            'price' => 29.99
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('name', 'Test Gaming Mouse');

        // 2. Fetch all products
        $fetchResponse = $this->getJson('/api/products');
        $fetchResponse->assertStatus(200)
                      ->assertJsonCount(1);
    }

    /** @test */
    public function can_create_warehouses()
    {
        $response = $this->postJson('/api/warehouses', [
            'name' => 'East Coast Hub',
            'location' => 'Boston, MA'
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('name', 'East Coast Hub');
    }

    /** @test */
    public function test_stock_transactions_and_balances()
    {
        // Setup mock objects
        $product = Product::create([
            'name' => 'Keyboard',
            'sku' => 'KB-01',
            'price' => 99.99
        ]);

        $warehouse = Warehouse::create([
            'name' => 'Storage A',
            'location' => 'Chicago'
        ]);

        // 1. Stock In: Add 100 keyboards
        $inResponse = $this->postJson('/api/stock/in', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 100,
            'reference' => 'PO-1001'
        ]);

        $inResponse->assertStatus(201)
                   ->assertJsonPath('current_stock', 100);

        // 2. Check balance
        $checkResponse = $this->getJson("/api/stock/product/{$product->id}/warehouse/{$warehouse->id}");
        $checkResponse->assertStatus(200)
                      ->assertJsonPath('current_stock', 100);

        // 3. Stock Out: Deduct 40 keyboards
        $outResponse = $this->postJson('/api/stock/out', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 40,
            'reference' => 'SO-2002'
        ]);

        $outResponse->assertStatus(201)
                    ->assertJsonPath('current_stock', 60);

        // 4. Stock Out: Deduct 70 keyboards (Should FAIL because only 60 are left)
        $failResponse = $this->postJson('/api/stock/out', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 70,
            'reference' => 'SO-2003'
        ]);

        $failResponse->assertStatus(422)
                     ->assertJsonStructure(['message', 'errors']);
    }
}
