<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Services\AccountService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->accountService->getAccountByUser($id);
            return $this->tResponseOK($result);
        } catch (ModelNotFoundException $e) {
            return $this->tResponseFail('Usuário não encontrado', Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->tResponseFail($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param TransactionRequest $transactionRequest
     * @return JsonResponse
     */
    public function transaction(TransactionRequest $transactionRequest): JsonResponse
    {
        try {
            $result = $this->accountService->createTransaction($transactionRequest->all());
            return $this->tResponseOK($result, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->tResponseFail($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function extractAccount(int $id): JsonResponse
    {
        try {
            $result = $this->accountService->getExtractAccount($id);
            return $this->tResponseOK($result, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->tResponseFail('Usuário não encontrado', Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->tResponseFail($exception->getMessage(), $exception->getCode());
        }
    }
}
