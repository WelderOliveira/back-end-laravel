<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Escolha aleatoriamente entre 1 (CNPJ) e 2 (CPF)
        $idTipoUsuario = $this->faker->randomElement([1, 2]);
        $cpfCnpj = $idTipoUsuario === 1 ? $this->generateCpf() : $this->generateCnpj();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'cpf_cnpj' => $cpfCnpj,
            'id_tipo_usuario' => $idTipoUsuario,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Generate a random CPF.
     */
    private function generateCpf(): string
    {
        return $this->faker->numerify('###########'); // 11 dígitos
    }

    /**
     * Generate a random CNPJ.
     */
    private function generateCnpj(): string
    {
        return $this->faker->numerify('##############'); // 14 dígitos
    }
}
