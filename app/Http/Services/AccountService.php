<?php

namespace App\Http\Services;

use App\Http\Constants\RequestConstant;
use App\Http\Constants\TypeConstant;
use App\Models\AccountModel;
use App\Models\TransactionModel;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class AccountService
{
    /**
     * @param int $user
     * @return array
     * @throws Exception
     */
    public function getAccountByUser(int $user): array
    {
        $user = $this->getUser($user);

        if (empty($user)) {
            throw new Exception('Usuário não encontrado.', Response::HTTP_NOT_FOUND);
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
            'type_account' => $user->type->st_description ?? null,
            'value' => $user->account->value,
        ];
    }

    /**
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function getExtractAccount(int $id): array
    {
        $user = $this->getUser($id);

        if (empty($user)) {
            throw new Exception('Conta não encontrada', Response::HTTP_NOT_FOUND);
        }

        $extractAccount = $this->verifyExtractAccount($user->id);

        return array_map(function ($item) {
            return [
                'value' => $item['value'],
                'created_at' => $item['created_at'],
                'payee_name' => $item['payee']['name'],
            ];
        }, $extractAccount);
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function createTransaction(array $params): array
    {
        $accountPayer = $this->getUser($params['payer']);
        $accountPayee = $this->getUser($params['payee']);

        if (empty($accountPayer) || empty($accountPayee)) {
            throw new Exception('Conta não encontrada.', Response::HTTP_NOT_FOUND);
        }

        if (TypeConstant::CNPJ === $accountPayer->type_id) {
            throw new Exception('Lojistas não podem realizar transferências.', Response::HTTP_NOT_ACCEPTABLE);
        }

        if (!$this->checkSufficientBalance($accountPayer->account->value, $params['value'])) {
            throw new Exception('Saldo insuficiente.', Response::HTTP_UNAUTHORIZED);
        }

        $accountPayer->account->value -= $params['value'];
        $accountPayee->account->value += $params['value'];

        $verifyAuthorization = RequestService::request(RequestConstant::AUTHORIZATION_REQUEST);

        if (!$verifyAuthorization['data']['authorization']) {
            throw new Exception('Transação não autorizada!', Response::HTTP_UNAUTHORIZED);
        }

        $this->saveAccount($accountPayee->account->toArray());

        $this->saveTransaction($params);
        // TODO ENVIAR NOTIFICAÇÃO PARA FILA
        return $this->saveAccount($accountPayer->account->toArray())->only(['value']);
    }

    /**
     * @param float $balance
     * @param float $value
     * @return bool
     */
    private function checkSufficientBalance(float $balance, float $value): bool
    {
        return $balance >= $value;
    }

    /**
     * @param array $params
     * @return AccountModel
     */
    private function saveAccount(array $params): AccountModel
    {
        return AccountModel::updateOrCreate(['id' => $params['id']], $params);
    }

    /**
     * @param array $params
     * @return void
     */
    private function saveTransaction(array $params): void
    {
        TransactionModel::create([
            'value' => $params['value'],
            'payer_id' => $params['payer'],
            'payee_id' => $params['payee']
        ]);
    }

    /**
     * @param $user
     * @return User|null
     */
    private function getUser($user): ?User
    {
        return User::findOrFail($user);
    }

    /**
     * @param $id
     * @return Collection|array
     */
    private function verifyExtractAccount($id): Collection|array
    {
        return TransactionModel::with(['payee'])
            ->where('payer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
