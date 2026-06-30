<?php

namespace App\Console\Commands;

use App\Models\SelfEmployedRecord;
use App\Http\Integrations\FNS\FNSConnector;
use App\Http\Integrations\FNS\Requests\taxpayerStatus;
use Illuminate\Console\Command;

class CheckSelfEmployedStatus extends Command
{
    protected $signature = 'self-employed:check-status';
    protected $description = 'Проверяет статус самозанятых через API ФНС (2 запроса в минуту)';

    public function handle()
    {
        // Берём 2 записи со статусом notVerifiable, отсортированные по дате создания
        $records = SelfEmployedRecord::where('verification_status', 'notVerifiable')
            ->orderBy('created_at', 'asc')
            ->take(2)
            ->get();

        foreach ($records as $record) {
            try {
                $connector = new FNSConnector();
                $query = new taxpayerStatus($record->inn);
                $response = $connector->send($query);
                $data = $response->json();

                $isSelfEmployed = false;
                if (isset($data['status'])) {
                    $isSelfEmployed = (bool)$data['status'];
                } elseif (isset($data['is_self_employed'])) {
                    $isSelfEmployed = (bool)$data['is_self_employed'];
                }

                // Обновляем статус
                $record->verification_status = $isSelfEmployed ? 'verified' : 'failedVerification';
                $record->save();

                \Log::info('Статус обновлён', [
                    'inn' => $record->inn,
                    'status' => $record->verification_status
                ]);
            } catch (\Exception $e) {
                \Log::error('Ошибка при проверке статуса самозанятого', [
                    'inn' => $record->inn,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // В случае ошибки тоже обновляем статус, чтобы не проверять эту запись повторно в следующем запуске
                $record->verification_status = 'failedVerification';
                $record->save();
            }
        }

        $this->info('Проверка статуса самозанятых завершена. Обработано записей: ' . $records->count());
        return Command::SUCCESS;
    }
}

