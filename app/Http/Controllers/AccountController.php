<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Services\AccountService;
use App\Models\AccountModel;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {

    }

    /**
     * @param User $id
     * @return JsonResponse
     */
    public function show(User $id): JsonResponse
    {
        try {
            $result = $this->accountService->getAccountByUser($id);
            return $this->tResponseOK($result);
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
     * @param User $id
     * @return JsonResponse
     */
    public function extractAccount(User $id): JsonResponse
    {
        try {
            $result = $this->accountService->getExtractAccount($id);
            return $this->tResponseOK($result, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->tResponseFail($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountModel $accountModel): void
    {

    }
}
