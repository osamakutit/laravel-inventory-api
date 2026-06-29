<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Warehouses
        $w1 = \App\Models\Warehouse::create([
            'name' => 'Main Warehouse',
            'location' => 'Chicago, IL',
        ]);
        $w2 = \App\Models\Warehouse::create([
            'name' => 'West Coast Hub',
            'location' => 'Seattle, WA',
        ]);
        $w3 = \App\Models\Warehouse::create([
            'name' => 'East Coast Fulfillment',
            'location' => 'Boston, MA',
        ]);

        // Seed Products
        $p1 = \App\Models\Product::create([
            'name' => 'Wireless Ergonomic Mouse',
            'sku' => 'MOUSE-W-01',
            'description' => 'Rechargeable wireless ergonomic mouse with adjustable DPI.',
            'price' => 49.99,
        ]);
        $p2 = \App\Models\Product::create([
            'name' => 'Mechanical RGB Keyboard',
            'sku' => 'KEYBOARD-M-02',
            'description' => 'Tactile blue switches mechanical keyboard with RGB backlighting.',
            'price' => 119.99,
        ]);
        $p3 = \App\Models\Product::create([
            'name' => 'UltraWide 34" Monitor',
            'sku' => 'MONITOR-U-03',
            'description' => '34-inch curved WQHD IPS display for seamless multitasking.',
            'price' => 449.99,
        ]);
        $p4 = \App\Models\Product::create([
            'name' => 'Noise Cancelling Headset',
            'sku' => 'HEADSET-N-04',
            'description' => 'Wireless over-ear headset with active noise cancellation.',
            'price' => 89.99,
        ]);

        // Seed initial Stock In movements
        \App\Models\StockMovement::create([
            'product_id' => $p1->id,
            'warehouse_id' => $w1->id,
            'type' => 'IN',
            'quantity' => 150,
            'reference' => 'PO-001 (Initial Setup)',
        ]);
        \App\Models\StockMovement::create([
            'product_id' => $p1->id,
            'warehouse_id' => $w2->id,
            'type' => 'IN',
            'quantity' => 50,
            'reference' => 'PO-002',
        ]);
        \App\Models\StockMovement::create([
            'product_id' => $p2->id,
            'warehouse_id' => $w1->id,
            'type' => 'IN',
            'quantity' => 80,
            'reference' => 'PO-001 (Initial Setup)',
        ]);
        \App\Models\StockMovement::create([
            'product_id' => $p3->id,
            'warehouse_id' => $w3->id,
            'type' => 'IN',
            'quantity' => 30,
            'reference' => 'PO-003',
        ]);
    }
}
