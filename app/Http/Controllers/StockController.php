<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Helper to get current stock balance of a product in a warehouse.
     */
    private function calculateCurrentStock(int $productId, int $warehouseId): int
    {
        $in = StockMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'IN')
            ->sum('quantity');

        $out = StockMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'OUT')
            ->sum('quantity');

        return (int) ($in - $out);
    }

    /**
     * Stock In - Add inventory.
     */
    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
        ]);

        $validated['type'] = 'IN';

        $movement = StockMovement::create($validated);

        return response()->json([
            'message' => 'Stock added successfully',
            'movement' => $movement,
            'current_stock' => $this->calculateCurrentStock($validated['product_id'], $validated['warehouse_id'])
        ], 201);
    }

    /**
     * Stock Out - Deduct inventory with checks.
     */
    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
        ]);

        $currentStock = $this->calculateCurrentStock($validated['product_id'], $validated['warehouse_id']);

        if ($currentStock < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock in the specified warehouse.',
                'errors' => [
                    'quantity' => [
                        "Requested: {$validated['quantity']}, Available: {$currentStock}"
                    ]
                ]
            ], 422);
        }

        $validated['type'] = 'OUT';

        $movement = StockMovement::create($validated);

        return response()->json([
            'message' => 'Stock deducted successfully',
            'movement' => $movement,
            'current_stock' => $currentStock - $validated['quantity']
        ], 201);
    }

    /**
     * Get stock of a product by warehouse.
     */
    public function getStockByWarehouse($productId, $warehouseId)
    {
        $product = Product::find($productId);
        $warehouse = Warehouse::find($warehouseId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        $currentStock = $this->calculateCurrentStock($productId, $warehouseId);

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku
            ],
            'warehouse' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name
            ],
            'current_stock' => $currentStock
        ], 200);
    }

    /**
     * Get product stock breakdown across all warehouses.
     */
    public function getProductStockAcrossWarehouses($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $warehouses = Warehouse::all();
        $stockBreakdown = [];
        $totalStock = 0;

        foreach ($warehouses as $warehouse) {
            $stock = $this->calculateCurrentStock($product->id, $warehouse->id);
            if ($stock > 0) {
                $stockBreakdown[] = [
                    'warehouse_id' => $warehouse->id,
                    'warehouse_name' => $warehouse->name,
                    'current_stock' => $stock
                ];
                $totalStock += $stock;
            }
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku
            ],
            'total_stock' => $totalStock,
            'warehouses' => $stockBreakdown
        ], 200);
    }
}
