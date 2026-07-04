<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductGroupController extends BaseController
{
    public function index(Request $request)
    {
        $this->shareCommonData();
        return view('dashboard.index', [
            'View' => 'dashboard.admin.productGroup.index',
            'title' => 'Управление группами товаров | Администрирование',
            'PageName' => 'Группы товаров',
            'InPageName' => 'Управление группами товаров',
            'CreateObject' => '/admin/productGroups/create'
        ]);
    }

    public function ajax(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);
            $search = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 1);
            $orderDir = $request->input('order.0.dir', 'asc');

            $columnMap = [
                1 => 'name',
                2 => 'parent_id',
                3 => 'products_count'
            ];

            $orderBy = $columnMap[$orderColumn] ?? 'name';

            $query = ProductGroup::with('parent')
                ->withCount('products')
                ->withCount('children');

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('parent', function($pq) use ($search) {
                            $pq->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            if ($request->has('parent_id') && $request->parent_id !== null && $request->parent_id !== '') {
                $query->where('parent_id', $request->parent_id);
            }

            $totalRecords = $query->count();

            $groups = $query->orderBy($orderBy, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            $formattedGroups = [];
            foreach ($groups as $group) {
                // Формируем URL изображения
                $imageUrl = '';
                if ($group->image) {
                    // Проверяем, является ли путь уже полным URL
                    if (filter_var($group->image, FILTER_VALIDATE_URL)) {
                        $imageUrl = $group->image;
                    } else {
                        $imageUrl = asset('/' . $group->image);
                    }
                }

                $formattedGroups[] = [
                    'id' => $group->id,
                    'name' => htmlspecialchars($group->name ?? 'Без названия'),
                    'image' => $imageUrl,
                    'parent' => $group->parent ? htmlspecialchars($group->parent->name) : '—',
                    'products_count' => $group->products_count ?? 0,
                    'children_count' => $group->children_count ?? 0
                ];
            }

            return response()->json([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$totalRecords,
                'data' => $formattedGroups
            ]);

        } catch (\Exception $e) {
            Log::error('ProductGroup DataTable error: ' . $e->getMessage());

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Произошла ошибка при загрузке данных'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $group = ProductGroup::findOrFail($id);
            $groupName = $group->name;

            $childrenCount = $group->children()->count();
            if ($childrenCount > 0) {
                return response()->json([
                    'success' => false,
                    'error' => "Невозможно удалить группу \"{$groupName}\", так как у нее есть {$childrenCount} дочерних групп. Сначала удалите или переместите дочерние группы."
                ], 400);
            }

            $productsCount = $group->products()->count();
            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'error' => "Невозможно удалить группу \"{$groupName}\", так как в ней находится {$productsCount} товаров. Сначала удалите или переместите товары."
                ], 400);
            }

            $group->delete();

            return response()->json([
                'success' => true,
                'message' => "Группа \"{$groupName}\" успешно удалена"
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting product group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Произошла ошибка при удалении группы'
            ], 500);
        }
    }

    public function create() {
        $this->shareCommonData();
        $productGroups = ProductGroup::rootGroups()->get();
        return view('dashboard.index', [
            'title' => 'Создать | Управление группами товаров | Администрирование',
            'PageName' => 'Группы товаров',
            'InPageName' => 'Создать группу товаров',
            'productGroups' => $productGroups,
            'View' => 'dashboard.admin.productGroup.show',
        ]);
    }

    /**
     * Сохранение новой категории
     */
    public function store(Request $request)
    {
        $this->shareCommonData();

        // Валидация
        $rules = [
            'name' => 'required|string|max:255|unique:productGroups,name',
            'parent_id' => 'nullable|exists:productGroups,id',
            'ShopMode'=> 'required|integer',
        ];

        // Для корневых категорий (без родителя) изображение обязательно
        if (empty($request->parent_id)) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:5120'; // 5MB max
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
        }

        $request->validate($rules, [
            'name.required' => 'Название категории обязательно',
            'name.unique' => 'Категория с таким названием уже существует',
            'image.required' => 'Для корневой категории необходимо загрузить изображение',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: JPEG, PNG, JPG',
            'image.max' => 'Максимальный размер файла 5 МБ',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'parent_id' => $request->parent_id ?: null,
                'ShopMode'=> $request->ShopMode,
            ];

            // Обработка изображения
            if ($request->hasFile('image')) {
                $imagePath = $this->uploadImage($request->file('image'));
                $data['image'] = $imagePath;
            }

            $category = ProductGroup::create($data);

            return redirect()
                ->route('admin.productGroups.index')
                ->with('success', "Категория \"{$category->name}\" успешно создана");

        } catch (\Exception $e) {
            Log::error('Error creating product group: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('message', 'Произошла ошибка при создании категории: ' . $e->getMessage());
        }
    }

    /**
     * Форма редактирования категории
     */
    public function edit($id)
    {
        $this->shareCommonData();
        $category = ProductGroup::with('children')->findOrFail($id);
        $productGroups = ProductGroup::rootGroups()->get();

        return view('dashboard.index', [
            'title' => 'Редактировать | Управление группами товаров | Администрирование',
            'PageName' => 'Группы товаров',
            'InPageName' => 'Редактирование группы товаров',
            'productGroups' => $productGroups,
            'category' => $category,
            'View' => 'dashboard.admin.productGroup.show',
        ]);
    }

    /**
     * Обновление категории
     */
    public function update(Request $request, $id)
    {
        $this->shareCommonData();
        $category = ProductGroup::findOrFail($id);

        // Валидация
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productGroups', 'name')->ignore($category->id)
            ],
            'parent_id' => [
                'nullable',
                'exists:productGroups,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('Нельзя выбрать текущую категорию как родительскую.');
                    }
                    // Проверка на циклическую зависимость
                    if ($value && $this->isChildCategory($category->id, $value)) {
                        $fail('Нельзя выбрать дочернюю категорию как родительскую.');
                    }
                },
            ],
            'ShopMode'=> 'required|integer',
            'remove_image' => 'nullable|boolean',
        ];

        // Правила для изображения
        if (empty($request->parent_id) && empty($category->image) && !$request->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
        } elseif ($request->hasFile('image')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:5120';
        }

        $request->validate($rules, [
            'name.required' => 'Название категории обязательно',
            'name.unique' => 'Категория с таким названием уже существует',
            'image.required' => 'Для корневой категории необходимо загрузить изображение',
            'parent_id.exists' => 'Выбранная родительская категория не существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: JPEG, PNG, JPG',
            'image.max' => 'Максимальный размер файла 5 МБ',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'parent_id' => $request->parent_id ?: null,
                'ShopMode'=> $request->ShopMode,
            ];

            // Удаление изображения
            if ($request->remove_image == '1' && $category->image) {
                $this->deleteImage($category->image);
                $data['image'] = null;
            }

            // Загрузка нового изображения
            if ($request->hasFile('image')) {
                // Удаляем старое изображение, если есть
                if ($category->image) {
                    $this->deleteImage($category->image);
                }
                $imagePath = $this->uploadImage($request->file('image'));
                $data['image'] = $imagePath;
            }

            $category->update($data);

            return redirect()
                ->route('admin.productGroups.index')
                ->with('success', "Категория \"{$category->name}\" успешно обновлена");

        } catch (\Exception $e) {
            Log::error('Error updating product group: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('message', 'Произошла ошибка при обновлении категории: ' . $e->getMessage());
        }
    }

    /**
     * Загрузка изображения
     */
    private function uploadImage($image)
    {
        $destinationPath = public_path('assets/dashboard/images/group_images');

        // Создаем директорию, если её нет
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Генерируем уникальное имя файла
        $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Перемещаем файл
        $image->move($destinationPath, $fileName);

        // Возвращаем путь относительно public
        return 'assets/dashboard/images/group_images/' . $fileName;
    }

    /**
     * Удаление изображения
     */
    private function deleteImage($imagePath)
    {
        $fullPath = public_path($imagePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Проверка, является ли категория $childId дочерней для $parentId
     */
    private function isChildCategory($childId, $parentId)
    {
        $category = ProductGroup::find($parentId);
        if (!$category) return false;

        $childrenIds = $this->getAllChildrenIds($category);
        return in_array($childId, $childrenIds);
    }

    /**
     * Получение всех ID дочерних категорий
     */
    private function getAllChildrenIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildrenIds($child));
        }
        return $ids;
    }
}
