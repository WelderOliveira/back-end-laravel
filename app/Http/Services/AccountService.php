<?php

namespace App\Http\Services;

use App\Models\AccountModel;
use App\Models\TransactionModel;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class AccountService
{
    /**
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function getAccountByUser(User $user): array
    {
        $account = $this->verifyAccountByUser($user);

        if (empty($account)) {
            throw new Exception('Conta não encontrada', Response::HTTP_NOT_FOUND);
        }

        return $account->only(['value']);
    }

    /**
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function getExtractAccount(User $user): array
    {
        if (empty($this->verifyAccountByUser($user))) {
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
        $accountPayer = User::with(['account'])->findOrFail($params['payer']);
        $accountPayee = User::with(['account'])->findOrFail($params['payee']);

        if (empty($accountPayer) || empty($accountPayee)) {
            throw new Exception('Conta não encontrada', Response::HTTP_NOT_FOUND);
        }

        if (1 === $accountPayer->type_id) {
            throw new Exception('Lojistas não podem realizar transferências ', Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($accountPayer->account->value < $params['value']) {
            throw new Exception('Saldo insuficiente', Response::HTTP_UNAUTHORIZED);
        }

        $accountPayer->account->value = $accountPayer->account->value - $params['value'];
        $accountPayee->account->value = $accountPayee->account->value + $params['value'];
        // TODO VERIFICAR SERVIÇO EXTERNO
        $this->saveAccount($accountPayee->account->toArray());

        $this->saveTransaction($params);
        // TODO ENVIAR NOTIFICAÇÃO PARA FILA
        return $this->saveAccount($accountPayer->account->toArray())->only(['value']);
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
     * @return AccountModel|null
     */
    private function verifyAccountByUser($user): ?AccountModel
    {
        return $user->account()->first();
    }

    /**
     * @param $id
     * @return Collection|array
     */
    private function verifyExtractAccount($id): Collection|array
    {
        return TransactionModel::with(['payee'])->where('payer_id', $id)->orderBy('created_at', 'desc')
            ->get()->toArray();
    }
}
