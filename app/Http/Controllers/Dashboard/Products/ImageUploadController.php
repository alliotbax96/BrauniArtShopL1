<?php

namespace App\Http\Controllers\Dashboard\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use App\Http\Controllers\Dashboard\BaseController;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;

class ImageUploadController extends BaseController
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!Auth::user()->groupInfo()->hasPermission('create_products') && !Auth::user()->groupInfo()->hasPermission('edit_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        if (!$request->hasFile('file')) {
            return response()->json([]);
        }

        $files = $request->file('file');
        $response = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($files as $file) {
            $error = '';
            $data = '';

            try {
                // Валидация файла
                $this->validateFile($file, $allowedExtensions);

                // Информация об изображении
                $info = @getimagesize($file->getPathname());
                if (empty($info[0]) || empty($info[1]) || !in_array($info[2], [1, 2, 3])) {
                    throw new \Exception('Недопустимый тип файла');
                }

                // Генерируем уникальное имя
                $name = time() . '-' . Str::random(10);
                $filename = $name . '.' . $file->getClientOriginalExtension();
                $thumbName = $name . '-thumb.' . $file->getClientOriginalExtension();

                // Сохраняем оригинал в S3
                $filePath = Storage::disk('s3')->putFileAs(
                    'uploads/tmp',
                    $file,
                    $filename
                );

                $Sfilename = 'uploads/tmp/' . $name . '.' . $file->getClientOriginalExtension();
                Log::info('Generated S3 URL: ' . Storage::disk('s3')->url($Sfilename));

                if ($filePath) {
                    // Создаём миниатюру — передаём путь в S3, а не URL!
                    $this->createThumbnailOnS3($Sfilename, $thumbName, $info);


                    // Формируем HTML с URL из S3
                    $data = $this->buildImageHtml(
                        $filename,
                        $thumbName,
                        Storage::disk('s3')->url('uploads/tmp/')
                    );
                } else {
                    throw new \Exception('Не удалось загрузить файл.');
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            $response[] = ['error' => $error, 'data' => $data];
        }

        return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
    }
    /**
     * Валидация файла
     */
    private function validateFile($file, array $allowedExtensions): void
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \Exception('Не удалось загрузить файл.');
        }

        if (!$file->isValid()) {
            throw new \Exception('Некорректный файл.');
        }

        $extension = mb_strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Недопустимый тип файла.');
        }
    }
    /**
     * Создание миниатюры на S3
     */
    private function createThumbnailOnS3(string $sourcePath, string $thumbName, array $imageInfo): void
    {
        try {
            // Загружаем оригинал из S3 во временный файл
            $tempPath = sys_get_temp_dir() . '/' . basename($sourcePath);
            $fileContents = Storage::disk('s3')->get($sourcePath); // Загружаем из S3
            file_put_contents($tempPath, $fileContents);

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Высота превью 200px, ширина рассчитывается автоматически
            $h = 200;
            $w = ($h > $height) ? $width : ceil($h / ($height / $width));

            $img = Image::read($tempPath);

            // Ресайз с сохранением пропорций
            $img->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Сохраняем миниатюру обратно в S3
            switch ($imageInfo[2]) {
                case 1: // GIF
                    $encodedImage = $img->toGif();
                    break;
                case 2: // JPEG
                    $encodedImage = $img->toJpeg(100);
                    break;
                case 3: // PNG
                    $encodedImage = $img->toPng();
                    break;
                default:
                    throw new \Exception('Неподдерживаемый формат изображения');
            }

            Storage::disk('s3')->put(
                'uploads/tmp/' . $thumbName,
                (string) $encodedImage,
                'public'
            );
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Очищаем временные файлы
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    /**
     * Формирование HTML-разметки для изображения
     */
    private function buildImageHtml(string $filename, string $thumbName, string $baseUrl): string
    {
        return <<<HTML
<div class="col-sm-6">
    <div class="card stretch stretch-full">
        <div class="card-body p-0 ht-200 position-relative">
            <img src="{$baseUrl}{$thumbName}" class="img-fluid ht-200" alt="">
            <div class="position-absolute" style="top: 15px; right: 15px">
                <a href="javascript:void(0)" onclick="remove_img(this); return false;" class="avatar-text avatar-sm">
                    <i class="feather feather-x-circle"></i>
                </a>
            </div>
            <input type="hidden" name="images[]" value="uploads/tmp/{$filename}">
        </div>
    </div>
</div>
HTML;
    }

    /**
     * Удаление изображения
     */
    public function deleteImage(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        if(!Auth::user()->groupInfo()->hasPermission('create_products') && !Auth::user()->groupInfo()->hasPermission('edit_products')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        try {
            $imagePath = $request->input('image');

            if (!$imagePath) {
                return response()->json(['error' => 'Путь к изображению не указан'], 400);
            }

            // Логируем входящий путь для отладки
            Log::info('Delete image request', [
                'product_id' => $id,
                'image_path' => $imagePath,
                'full_path' => $imagePath
            ]);

            $deletedCount = 0;
            $errors = [];

            // Проверяем существование файла перед удалением
            if (Storage::disk('s3')->exists($imagePath)) {
                try {
                    $result = Storage::disk('s3')->delete($imagePath);

                    if ($result) {
                        $deletedCount++;
                        Log::info('File deleted from S3', ['path' => $imagePath]);
                    } else {
                        $errors[] = 'Failed to delete file from S3: ' . $imagePath;
                        Log::warning('S3 delete returned false', ['path' => $imagePath]);
                    }
                } catch (\Exception $e) {
                    $errors[] = 'S3 delete error: ' . $e->getMessage();
                    Log::error('S3 delete exception', [
                        'path' => $imagePath,
                        'exception' => $e->getMessage()
                    ]);
                }
            } else {
                Log::warning('File not found in S3', ['path' => $imagePath]);
                $errors[] = 'File not found: ' . $imagePath;
            }

            // Удаляем запись из базы данных
            $dbDeleted = ProductImage::where('productId', $id)
                ->where('url', $imagePath)
                ->delete();

            Log::info('Database records deleted', [
                'product_id' => $id,
                'image_path' => $imagePath,
                'count' => $dbDeleted
            ]);

            // Формируем ответ
            $response = [
                'success' => $deletedCount > 0,
                'files_deleted' => $deletedCount,
                'database_records_deleted' => $dbDeleted,
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            if ($deletedCount > 0) {
                $response['message'] = 'Изображение успешно удалено';
            } elseif ($dbDeleted > 0) {
                $response['message'] = 'Запись из базы данных удалена (файл не найден в S3)';
            } else {
                $response['message'] = 'Файл не найден ни в S3, ни в базе данных';
                $response['success'] = false;
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('Critical error in deleteImage method', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Внутренняя ошибка сервера: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }


}
