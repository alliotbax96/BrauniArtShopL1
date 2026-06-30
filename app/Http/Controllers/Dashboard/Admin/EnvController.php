<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Dashboard\BaseController;

class EnvController extends BaseController
{
    /**
     * Показать форму редактирования .env
     */
    public function index(): View
    {
        $this->shareCommonData();
        $envData = $this->getEnvData();
        $envBackups = $this->getBackupsList();
        $PageName = 'Администрирование';
        $InPageName = 'Настройки системы';
        $title = 'Настройки системы | Единая система BaID';
        $View = 'dashboard.admin.env.index';
        return view('dashboard.index', compact('envData', 'envBackups', 'View', 'PageName', 'InPageName', 'title'));
    }

    /**
     * Обновить .env файл
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'env_data' => 'required|array',
            'env_data.*' => 'nullable|string',
        ]);

        try {
            // Создаем бэкап перед обновлением
            $this->createBackup();

            // Получаем текущие данные
            $newEnvData = $request->input('env_data', []);

            // Читаем текущий .env
            $envPath = base_path('.env');
            $currentEnv = File::get($envPath);

            // Обновляем значения
            foreach ($newEnvData as $key => $value) {
                $key = strtoupper(trim($key));
                $value = trim($value);

                // Если значение содержит пробелы, оборачиваем в кавычки
                if (str_contains($value, ' ')) {
                    $value = '"' . $value . '"';
                }

                // Проверяем существует ли ключ
                if (preg_match("/^{$key}=/m", $currentEnv)) {
                    $currentEnv = preg_replace(
                        "/^{$key}=.*/m",
                        "{$key}={$value}",
                        $currentEnv
                    );
                } else {
                    // Если ключ новый, добавляем в конец
                    $currentEnv .= PHP_EOL . "{$key}={$value}";
                }
            }

            // Сохраняем обновленный .env
            File::put($envPath, $currentEnv);

            // Очищаем кэш конфигурации
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()
                ->route('admin.env.index')
                ->with('success', '.env файл успешно обновлен');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.env.index')
                ->with('error', 'Ошибка при обновлении .env файла: ' . $e->getMessage());
        }
    }

    /**
     * Создать бэкап .env файла
     */
    public function createBackup(): RedirectResponse|string
    {
        try {
            $envPath = base_path('.env');
            $backupPath = storage_path('app/env-backups/');

            // Создаем директорию если не существует
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupFile = $backupPath . "env_backup_{$timestamp}.env";

            File::copy($envPath, $backupFile);

            return redirect()
                ->route('admin.env.index')
                ->with('success', 'Бэкап создан: ' . basename($backupFile));

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.env.index')
                ->with('error', 'Ошибка при создании бэкапа: ' . $e->getMessage());
        }
    }

    /**
     * Восстановить бэкап
     */
    public function restoreBackup(string $filename): RedirectResponse
    {
        try {
            $backupPath = storage_path("app/env-backups/{$filename}");

            if (!File::exists($backupPath)) {
                throw new \Exception('Файл бэкапа не найден');
            }

            // Создаем бэкап текущего состояния перед восстановлением
            $this->createBackupAction();

            // Восстанавливаем из бэкапа
            $envPath = base_path('.env');
            File::copy($backupPath, $envPath);

            // Очищаем кэш
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()
                ->route('admin.env.index')
                ->with('success', 'Бэкап успешно восстановлен');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.env.index')
                ->with('error', 'Ошибка при восстановлении бэкапа: ' . $e->getMessage());
        }
    }

    /**
     * Удалить бэкап
     */
    public function deleteBackup(string $filename): RedirectResponse
    {
        try {
            $backupPath = storage_path("app/env-backups/{$filename}");

            if (File::exists($backupPath)) {
                File::delete($backupPath);
            }

            return redirect()
                ->route('admin.env.index')
                ->with('success', 'Бэкап удален');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.env.index')
                ->with('error', 'Ошибка при удалении бэкапа: ' . $e->getMessage());
        }
    }

    /**
     * Получить данные из .env файла
     */
    private function getEnvData(): array
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            return [];
        }

        $content = File::get($envPath);
        $lines = explode("\n", $content);
        $envData = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Пропускаем пустые строки и комментарии
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Парсим ключ=значение
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Убираем кавычки если они есть
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }

                // Группируем по категориям
                $category = $this->getCategory($key);
                $envData[$category][$key] = $value;
            }
        }

        return $envData;
    }

    /**
     * Получить список бэкапов
     */
    private function getBackupsList(): array
    {
        $backupPath = storage_path('app/env-backups/');

        if (!File::exists($backupPath)) {
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        // Сортируем по дате (новые сверху)
        usort($backups, function ($a, $b) {
            return strcmp($b['modified'], $a['modified']);
        });

        return $backups;
    }

    /**
     * Определить категорию для ключа
     */
    private function getCategory(string $key): string
    {
        $key = strtoupper($key);

        if (in_array($key, ['APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL'])) {
            return 'Приложение';
        } elseif (in_array($key, ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'])) {
            return 'База данных';
        } elseif (in_array($key, ['MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS'])) {
            return 'Почта';
        } elseif (in_array($key, ['CACHE_DRIVER', 'SESSION_DRIVER', 'QUEUE_CONNECTION'])) {
            return 'Кэш и сессии';
        } elseif (in_array($key, ['REDIS_HOST', 'REDIS_PASSWORD', 'REDIS_PORT'])) {
            return 'Redis';
        } elseif (in_array($key, ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET'])) {
            return 'AWS';
        } else {
            return 'Другие';
        }
    }

    /**
     * Создать бэкап (внутренний метод)
     */
    private function createBackupAction(): void
    {
        $envPath = base_path('.env');
        $backupPath = storage_path('app/env-backups/');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = $backupPath . "env_backup_{$timestamp}.env";

        File::copy($envPath, $backupFile);
    }
}
