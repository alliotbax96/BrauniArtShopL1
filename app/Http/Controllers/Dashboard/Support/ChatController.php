<?php

namespace App\Http\Controllers\Dashboard\Support;

use App\Events\NewChatCreated;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ChatController extends BaseController
{
    public function index()
    {
        $this->shareCommonData();

        $user = Auth::user();
        $hasStatusColumn = Schema::hasColumn('chats', 'status');

        if ($user->isAdmin()) {
            // Админ видит: свои чаты + неназначенные чаты покупателей + чаты продавцов
            $query = Chat::where(function($query) use ($user) {
                // Чаты, где админ является участником (назначенные ему)
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
                ->orWhere(function($query) {
                    // Неназначенные чаты покупателей
                    $query->where('type', 'buyer_support')
                        ->where(function($q) {
                            $q->whereNull('assigned_admin_id')
                                ->orWhere('assigned_admin_id', 0);
                        });
                })
                ->orWhere('type', 'support') // Все чаты продавцов
                ->with(['users', 'messages' => function($query) {
                    $query->latest()->first();
                }, 'assignedAdmin', 'client'])
                ->withCount(['messages as unread_count' => function($query) use ($user) {
                    $query->where('is_read', false)
                        ->where('user_id', '!=', $user->id);
                }]);

            if ($hasStatusColumn) {
                $chats = $query->orderByRaw("
                    CASE
                        WHEN type = 'buyer_support' AND status = 'waiting' THEN 1
                        WHEN type = 'buyer_support' AND status = 'active' THEN 2
                        WHEN type = 'support' THEN 3
                        ELSE 4
                    END
                ")
                    ->orderBy('updated_at', 'desc')
                    ->get();
            } else {
                $chats = $query->orderBy('updated_at', 'desc')->get();
            }
        } else {
            $chats = Chat::whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with(['users', 'messages' => function($query) {
                    $query->latest()->first();
                }])
                ->withCount(['messages as unread_count' => function($query) use ($user) {
                    $query->where('is_read', false)
                        ->where('user_id', '!=', $user->id);
                }])
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        $mainView = 'dashboard.support.index';
        return view('dashboard.index', compact('chats', 'mainView'));
    }

    public function ajax(Request $request)
    {
        try {
            $user = Auth::user();
            $chats = $this->getUserChatsForAjax($user);

            if ($request->has('check_new')) {
                $lastCheck = $request->session()->get('last_chat_check', now()->subMinutes(30));
                $newChats = $this->getNewChatsForAjax($user, $lastCheck);
                $request->session()->put('last_chat_check', now());
                return response()->json($newChats);
            }

            return response()->json($chats);
        } catch (\Exception $e) {
            Log::error('Error in ajax chat method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function show(Chat $chat)
    {
        $this->shareCommonData();

        $user = Auth::user();

        if (!$this->canAccessChat($user, $chat)) {
            abort(403, 'У вас нет доступа к этому чату');
        }

        // Автоматически подключаем админа к чату покупателя
        if ($chat->type === 'buyer_support' && $user->isAdmin()) {
            $needUpdate = false;

            if (!$chat->assigned_admin_id) {
                $chat->assigned_admin_id = $user->id;
                $needUpdate = true;
            }

            if ($chat->status === 'waiting') {
                $chat->status = 'active';
                $needUpdate = true;
            }

            if ($needUpdate) {
                $chat->save();
            }

            if (!$chat->hasUser($user)) {
                $chat->addUser($user);
            }
        }

        $chats = $this->getChatsList($user);

        $chat->load([
            'users',
            'messages' => function($query) {
                $query->orderBy('created_at', 'asc');
            },
            'assignedAdmin',
            'client'
        ]);

        Message::where('chat_id', $chat->id)
            ->where('is_read', false)
            ->where('user_id', '!=', $user->id)
            ->update(['is_read' => true]);

        $mainView = 'dashboard.support.show';
        return view('dashboard.index', compact('chat', 'chats', 'mainView'));
    }

    public function store()
    {
        try {
            DB::transaction(function () {
                $chat = Chat::create([
                    'name' => 'Запрос в поддержку',
                    'type' => 'support',
                ]);

                if (!$chat->addUser(Auth::user())) {
                    throw new \Exception('Не удалось добавить пользователя в чат');
                }

                $chat->update([
                    'name' => "Запрос в поддержку №{$chat->id}"
                ]);

                session(['chatId' => $chat->id]);
            });

            return redirect(route('seller.chat.show', session('chatId')));

        } catch (\Exception $e) {
            Log::error('Ошибка при создании чата поддержки: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'Не удалось создать чат поддержки.'
            ]);
        }
    }

    public function delete(int $chatId)
    {
        try {
            $chat = Chat::find($chatId);

            if (!$chat) {
                return response()->json(['success' => false, 'message' => 'Чат не найден'], 404);
            }

            $user = Auth::user();

            if ($user->isAdmin() && in_array($chat->type, ['support', 'buyer_support'])) {
                $canDelete = true;
            } else {
                $canDelete = $chat->users()->where('user_id', $user->id)->exists();
            }

            if (!$canDelete) {
                return response()->json(['success' => false, 'message' => 'У вас нет прав'], 403);
            }

            DB::transaction(function () use ($chat) {
                foreach ($chat->messages as $message) {
                    if ($message->file_path && Storage::exists($message->file_path)) {
                        Storage::delete($message->file_path);
                    }
                }
                $chat->messages()->delete();
                $chat->users()->detach();
                $chat->delete();
            });

            return response()->json(['success' => true, 'message' => 'Чат удалён']);

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении чата: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка удаления'], 500);
        }
    }

    public function transferChat(Request $request, Chat $chat)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Доступ запрещен'], 403);
        }

        $request->validate(['admin_id' => 'required|exists:users,id']);

        $newAdmin = User::findOrFail($request->admin_id);

        if (!$newAdmin->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Пользователь не администратор'], 400);
        }

        $oldAdminId = $chat->assigned_admin_id;

        $chat->update(['assigned_admin_id' => $newAdmin->id]);
        $chat->addUser($newAdmin);

        Log::info('Чат передан', [
            'chat_id' => $chat->id,
            'from_admin' => $oldAdminId,
            'to_admin' => $newAdmin->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Чат передан сотруднику ' . $newAdmin->name
        ]);
    }

    public function getAdminsForTransfer()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false], 403);
        }

        $admins = User::whereHas('groups', function($query) {
            $query->where('type', 'admin');
        })
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->get();

        return response()->json(['success' => true, 'admins' => $admins]);
    }

    private function getUserChatsForAjax($user)
    {
        if ($user->isAdmin()) {
            return Chat::where(function($query) use ($user) {
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
                ->orWhere(function($query) {
                    $query->where('type', 'buyer_support')
                        ->where(function($q) {
                            $q->whereNull('assigned_admin_id')
                                ->orWhere('assigned_admin_id', 0);
                        });
                })
                ->orWhere('type', 'support')
                ->select('id', 'name', 'type', 'status', 'client_name', 'created_at', 'updated_at')
                ->get()
                ->map(function($chat) {
                    return [
                        'id' => $chat->id,
                        'name' => $chat->name,
                        'type' => $chat->type,
                        'status' => $chat->status,
                        'client_name' => $chat->client_name,
                        'created_at' => $chat->created_at,
                    ];
                });
        }

        return Chat::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->select('id', 'name', 'type', 'created_at', 'updated_at')
            ->get()
            ->map(function($chat) {
                return [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'created_at' => $chat->created_at,
                ];
            });
    }

    private function getNewChatsForAjax($user, $lastCheck)
    {
        if ($user->isAdmin()) {
            return Chat::where(function($query) use ($user) {
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
                ->orWhere(function($query) {
                    $query->where('type', 'buyer_support')
                        ->where(function($q) {
                            $q->whereNull('assigned_admin_id')
                                ->orWhere('assigned_admin_id', 0);
                        });
                })
                ->orWhere('type', 'support')
                ->where('created_at', '>', $lastCheck)
                ->select('id', 'name', 'type', 'status', 'client_name', 'created_at')
                ->get()
                ->map(function($chat) {
                    return [
                        'id' => $chat->id,
                        'name' => $chat->name,
                        'type' => $chat->type,
                        'status' => $chat->status,
                        'client_name' => $chat->client_name,
                        'created_at' => $chat->created_at,
                    ];
                });
        }

        return Chat::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('created_at', '>', $lastCheck)
            ->select('id', 'name', 'type', 'created_at')
            ->get()
            ->map(function($chat) {
                return [
                    'id' => $chat->id,
                    'name' => $chat->name,
                    'type' => $chat->type,
                    'created_at' => $chat->created_at,
                ];
            });
    }

    private function getChatsList($user)
    {
        $hasStatusColumn = Schema::hasColumn('chats', 'status');

        if ($user->isAdmin()) {
            $query = Chat::where(function($query) use ($user) {
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
                ->orWhere(function($query) {
                    $query->where('type', 'buyer_support')
                        ->where(function($q) {
                            $q->whereNull('assigned_admin_id')
                                ->orWhere('assigned_admin_id', 0);
                        });
                })
                ->orWhere('type', 'support')
                ->with(['users', 'messages' => function($query) {
                    $query->latest()->first();
                }, 'assignedAdmin', 'client'])
                ->withCount(['messages as unread_count' => function($query) use ($user) {
                    $query->where('is_read', false)
                        ->where('user_id', '!=', $user->id);
                }]);

            if ($hasStatusColumn) {
                return $query->orderByRaw("
                    CASE
                        WHEN type = 'buyer_support' AND status = 'waiting' THEN 1
                        WHEN type = 'buyer_support' AND status = 'active' THEN 2
                        WHEN type = 'support' THEN 3
                        ELSE 4
                    END
                ")
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }

            return $query->orderBy('updated_at', 'desc')->get();
        }

        return Chat::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['users', 'messages' => function($query) {
                $query->latest()->first();
            }])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('is_read', false)
                    ->where('user_id', '!=', $user->id);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    private function canAccessChat($user, Chat $chat): bool
    {
        if ($user->isAdmin() && $chat->type === 'support') {
            return true;
        }

        if ($user->isAdmin() && $chat->type === 'buyer_support') {
            if ($chat->assigned_admin_id === $user->id) {
                return true;
            }
            if (!$chat->assigned_admin_id || $chat->assigned_admin_id === 0) {
                return true;
            }
            if ($chat->hasUser($user)) {
                return true;
            }
            return false;
        }

        return $chat->hasUser($user);
    }
}
