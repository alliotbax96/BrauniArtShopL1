<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Models\SellerContract;

class SellersController extends BaseController
{
    public function index() {
        $this->shareCommonData();
        $View = 'dashboard.admin.sellers.index';
        $PageName = 'sellers';
        $InPageName = 'sellers';
        return view('dashboard.index', compact('View', 'PageName', 'InPageName'));
    }

    public function ajax(Request $request)
    {
        // Базовый запрос
        $query = Seller::with(['legalDetails', 'selfEmployedRecords', 'contacts'])
            ->select('sellers.*');

        // Поиск по названию в системе / юрлицу (можно расширить)
        if ($search = $request->get('search', [])['value'] ?? null) {
            $search = "%{$search}%";
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhereHas('legalDetails', fn ($q) => $q->where('company_name', 'like', $search))
                    ->orWhereHas('selfEmployedRecords', fn ($q) => $q->where('inn', 'like', $search));
            });
        }

        // Сортировка
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        if ($orderColumn !== null) {
            $columnName = $request->input('columns.' . $orderColumn . '.name');
            if ($columnName === 'name') {
                $query->orderBy('name', $orderDir);
            } elseif ($columnName === 'legal_name') {
                // сортировка по юрлицу: делаем join
                $query->join('seller_legal_details as sld', 'sellers.id', '=', 'sld.seller_id')
                    ->selectRaw('sellers.*, sld.company_name as legal_name_sort')
                    ->orderBy('sld.company_name', $orderDir)
                    ->groupBy('sellers.id');
            } else {
                $query->orderBy('created_at', $orderDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $totalFiltered = $query->count();

        // Пагинация для DataTable
        $limit = $request->input('length', 10);
        $offset = $request->input('start', 0);
        $data = $query->skip($offset)->take($limit)->get();

        // Формируем строки для DataTable (чтобы не делать N+1 запросов в JS)
        $rows = $data->map(function ($seller) {
            // Юрлицо: берём из связи (может быть несколько, но по ТЗ обычно одна)
            $legalDetail = $seller->legalDetails->first();
            $legalName = $legalDetail?->company_name ?? '—';

            // Договор: берём первый активный/последний (подстрой под свою логику)
            $contract = $seller->contacts->first(); // это SellerContract
            $contractId = $contract?->id ?? '—';
            $status = $contract?->status ?? 0;
            $contractStatus = $contract?->signed_status ?? 0;

            return [
                'id' => $seller->id,
                'name' => $seller->name,
                'legal_name' => $legalName,
                'contract_id' => $contractId,
                'contract_status' => $contractStatus,
                'status' => (int)$status,
            ];
        });

        return response()->json([
            'draw' => (int)$request->input('draw', 1),
            'recordsTotal' => Seller::count(),
            'recordsFiltered' => $totalFiltered,
            'data' => $rows->toArray(),
        ]);
    }

    public function updateContractStatus(Request $request, int $contract_id)
    {
        $contract = SellerContract::findOrFail($contract_id);

        $validated = $request->validate([
            'status'      => 'nullable|integer|in:0,1',
            'signed_status' => 'nullable|integer|in:0,1',
        ]);

        $updates = [];

        if (isset($validated['status'])) {
            $updates['status'] = $validated['status'];
        }

        if (isset($validated['signed_status'])) {
            $updates['signed_status'] = $validated['signed_status'];
        }

        if (!empty($updates)) {
            $contract->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Статус успешно обновлён',
            'data' => [
                'contract_id' => $contract->id,
                'status' => $contract->status,
                'signed_status' => $contract->signed_status,
            ],
        ]);
    }

}
