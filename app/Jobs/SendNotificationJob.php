<?php

namespace App\Jobs;

use App\Http\Constants\RequestConstant;
use App\Http\Services\RequestService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 10;

    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $response = RequestService::request(
                RequestConstant::NOTIFY_REQUEST,
                'post',
                data: $this->data
            );

            if (isset($response['status']) && 'error' === $response['status']) {
                throw new Exception($response['message']);
            }

            Log::debug('Notificação enviada com sucesso');
        } catch (Exception $exception) {
            Log::debug('Falha no envio de notificação ', ['error' => $exception->getMessage()]);

            throw $exception;
        }
    }
}
