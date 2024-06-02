<?php

namespace Database\Factories;

use App\Models\AccountModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountModel>
 */
class AccountModelFactory extends Factory
{
    protected $model = AccountModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
