<?php

namespace Database\Factories;

use App\Models\AccountModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

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
        $cpfCnpj = 1 === $idTipoUsuario ? $this->generateCnpj() : $this->generateCpf();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'cpf_cnpj' => $cpfCnpj,
            'type_id' => $idTipoUsuario,
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
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($user): void {
            // Cria uma conta para o usuário e vincula o ID da conta ao usuário
            $account = AccountModel::factory()->create();
            $user->account_id = $account->id;
            $user->save();
        });
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
