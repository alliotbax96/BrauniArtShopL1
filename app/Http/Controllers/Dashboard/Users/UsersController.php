<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;

class UsersController extends BaseController
{
    public function index(){
        if(!Auth::user()->groupInfo()->hasPermission('view_users')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $this->shareCommonData(); // вызываем один раз
        return view('dashboard.index', ['View'=>'dashboard.users.index', 'title'=>'Управление сотрудниками | Единая система BaID', 'PageName'=>'Управление сотрудниками', 'InPageName'=>'Сотрудники']);
    }

    public function ajax(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('view_users')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        $seller = Auth::user()->getFirstSeller();

        // Получаем параметры от DataTables
        $draw = request()->input('draw', 1);
        $start = request()->input('start', 0);
        $length = request()->input('length', 10);
        $search = request()->input('search.value', '');
        $orderColumn = request()->input('order.0.column', 0);
        $orderDir = request()->input('order.0.dir', 'desc');

        // Определяем поле для сортировки
        $columns = ['name', 'email', 'phone', 'group_name'];
        $orderField = $columns[$orderColumn] ?? 'name';

        // Базовый запрос с фильтрацией по продавцу
        $query = User::whereHas('sellers', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with('group');

        // Поиск по ФИО, email или телефону
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Фильтр по группе
        $groupId = request()->input('group_id');
        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        // Сортировка и пагинация
        $query->orderBy($orderField, $orderDir);
        $total = $query->count();
        $users = $query->skip($start)->take($length)->get();

        // Получаем все группы для выпадающего списка
        $groups = Group::where('type', '!=', 'admin')->get();

        // Форматируем данные
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'name' => htmlspecialchars($user->name),
                'email' => htmlspecialchars($user->email),
                'phone' => htmlspecialchars($user->phone),
                'group_id' => $user->groupId(),
                'group_name' => $user->group ? htmlspecialchars($user->group->name) : 'Не назначена',
                'groups' => $groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => htmlspecialchars($group->name),
                        'description' => htmlspecialchars($group->description),
                    ];
                })->toArray()
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    public function updateGroup(Request $request)
    {
        if(!Auth::user()->groupInfo()->hasPermission('edit_users')) {
            abort(403, 'У вас нет прав для просмотра данного раздела!');
        }
        // Валидация входящих данных
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'group_id' => 'required|integer|exists:groups,id'
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);

            // Для связи «многие‑ко‑многим» используем sync(), чтобы установить ровно одну группу
            // Если нужно добавлять группы без удаления предыдущих, используйте attach()
            $user->groups()->sync([$validated['group_id']]);

            return response()->json([
                'success' => true,
                'message' => 'Группа пользователя успешно обновлена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении группы: ' . $e->getMessage()
            ], 500);
        }
    }

}
