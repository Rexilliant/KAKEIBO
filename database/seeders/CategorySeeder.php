<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $defaultCategories = [
            // income
            [
                'name' => 'Gaji',
                'transaction_type' => 'income',
                'kakeibo_type' => 'income',
                'icon' => 'wallet',
                'color' => 'green',
            ],
            [
                'name' => 'Bonus',
                'transaction_type' => 'income',
                'kakeibo_type' => 'income',
                'icon' => 'gift',
                'color' => 'green',
            ],
            [
                'name' => 'Freelance',
                'transaction_type' => 'income',
                'kakeibo_type' => 'income',
                'icon' => 'briefcase',
                'color' => 'green',
            ],
            [
                'name' => 'Lainnya',
                'transaction_type' => 'income',
                'kakeibo_type' => 'income',
                'icon' => 'plus-circle',
                'color' => 'green',
            ],

            // needs
            [
                'name' => 'Makan',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'needs',
                'icon' => 'utensils',
                'color' => 'blue',
            ],
            [
                'name' => 'Transport',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'needs',
                'icon' => 'truck',
                'color' => 'blue',
            ],
            [
                'name' => 'Tagihan',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'needs',
                'icon' => 'receipt',
                'color' => 'blue',
            ],
            [
                'name' => 'Kebutuhan Rumah',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'needs',
                'icon' => 'home',
                'color' => 'blue',
            ],

            // wants
            [
                'name' => 'Nongkrong',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'wants',
                'icon' => 'coffee',
                'color' => 'yellow',
            ],
            [
                'name' => 'Hiburan',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'wants',
                'icon' => 'film',
                'color' => 'yellow',
            ],
            [
                'name' => 'Belanja Impulsif',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'wants',
                'icon' => 'shopping-bag',
                'color' => 'yellow',
            ],

            // culture
            [
                'name' => 'Buku',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'culture',
                'icon' => 'book',
                'color' => 'purple',
            ],
            [
                'name' => 'Kursus',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'culture',
                'icon' => 'academic-cap',
                'color' => 'purple',
            ],
            [
                'name' => 'Hobi Sehat',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'culture',
                'icon' => 'sparkles',
                'color' => 'purple',
            ],

            // unexpected
            [
                'name' => 'Darurat',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'unexpected',
                'icon' => 'exclamation-circle',
                'color' => 'red',
            ],
            [
                'name' => 'Obat',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'unexpected',
                'icon' => 'heart',
                'color' => 'red',
            ],
            [
                'name' => 'Servis',
                'transaction_type' => 'expense',
                'kakeibo_type' => 'unexpected',
                'icon' => 'wrench',
                'color' => 'red',
            ],
        ];

        foreach ($users as $user) {
            foreach ($defaultCategories as $category) {
                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $category['name'],
                        'transaction_type' => $category['transaction_type'],
                    ],
                    [
                        'kakeibo_type' => $category['kakeibo_type'],
                        'icon' => $category['icon'],
                        'color' => $category['color'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}