<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * @param string $message
     * @param mixed $code
     * @return JsonResponse
     */
    public function rollback(string $message, mixed $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        DB::rollBack();
        return $this->tResponse(false, [], $message, $code);
    }

    /**
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    public function tResponseOK(mixed $data, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->tResponse(true, $data, '', $code);
    }

    /**
     * @param string $message
     * @param mixed $code
     * @return JsonResponse
     */
    public function tResponseFail(string $message, mixed $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return $this->tResponse(false, [], $message, $code);
    }

    /**
     * @param bool $status
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function tResponse(bool $status, mixed $data, ?string $message, int $code): JsonResponse
    {
        return response()->json(
            [
                'status' => $status,
                'data' => $data,
                'message' => $message,
            ],
            $code
        );
    }
}
