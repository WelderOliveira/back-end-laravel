<?php

namespace Tests\Feature;

use App\Http\Services\AccountService;
use App\Models\AccountModel;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private AccountModel $account;
    private AccountService $accountService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpFaker();
        $this->user = User::factory()->create();
        $this->account = AccountModel::factory()->create();

        $this->accountService = $this->createMock(AccountService::class);
        $this->app->instance(AccountService::class, $this->accountService);

    }

    /**
     * @return void
     */
    public function testShowNoExistingAccount(): void
    {
        $response = $this->getJson('api/account/');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @return void
     */
    public function testTransactionSuccess(): void
    {
        $payer = User::factory()->hasAccount(['value' => 500])->create(['type_id' => 2]);
        $payee = User::factory()->hasAccount(['value' => 100])->create(['type_id' => 1]);

        $request = [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id
        ];

        $this->accountService->method('createTransaction')
            ->willReturn(['value' => 450]);

        $response = $this->postJson('/api/transfer', $request);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'status' => true,
                'data' => ['value' => 450],
                'message' => ''
            ]);
    }

    /**
     * @return void
     */
    public function testTransactionInsufficientBalance(): void
    {
        $payer = User::factory()->hasAccount(['value' => 20])->create(['type_id' => 2]);
        $payee = User::factory()->hasAccount(['value' => 100])->create(['type_id' => 1]);

        $request = [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id
        ];

        $this->accountService->method('createTransaction')
            ->willThrowException(new Exception('Saldo insuficiente.', Response::HTTP_UNAUTHORIZED));

        $response = $this->postJson('/api/transfer', $request);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'status' => false,
                'message' => 'Saldo insuficiente.'
            ]);
    }

    /**
     * @return void
     */
    public function testTransactionNotAuthorized(): void
    {
        $payer = User::factory()->hasAccount(['value' => 500])->create(['type_id' => 2]);
        $payee = User::factory()->hasAccount(['value' => 100])->create(['type_id' => 1]);

        $request = [
            'value' => 50,
            'payer' => $payer->id,
            'payee' => $payee->id
        ];

        $this->accountService->method('createTransaction')
            ->willThrowException(new Exception('Transação não autorizada!', Response::HTTP_UNAUTHORIZED));

        $response = $this->postJson('/api/transfer', $request);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'status' => false,
                'message' => 'Transação não autorizada!'
            ]);
    }
}
