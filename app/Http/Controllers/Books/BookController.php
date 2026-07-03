<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\BaseController;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\ProductGroup;

class BookController extends BaseController
{
    public function index(Request $request)
    {
        $this->shareCommonData($request);
        Cookie::queue('ShopMode', 1);

        $filters = [
            'category'      => $request->input('category'),
            'type'          => $request->input('type'), // ebook / audiobook
            'min_price'     => $request->input('min_price'),
            'max_price'     => $request->input('max_price'),
            'search'        => $request->input('search'),
            'author'        => $request->input('author'),
        ];

        // Валидация цен
        $minPrice = is_numeric($filters['min_price']) ? (float)$filters['min_price'] : null;
        $maxPrice = is_numeric($filters['max_price']) ? (float)$filters['max_price'] : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        $filters['min_price'] = $minPrice;
        $filters['max_price'] = $maxPrice;

        $perPage = $request->input('perPage', 18);
        $validPerPage = in_array($perPage, [9, 12, 18, 21, 24]) ? $perPage : 18;

        // Основной запрос: активные, одобренные книги
        $query = Book::query()
            ->active()
            ->approved();

        // Фильтры по типу
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'ebook') {
                $query->ebooks();
            } elseif ($filters['type'] === 'audiobook') {
                $query->audiobooks();
            }
        }

        // Поиск по названию или автору
        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query->where('name', 'like', $search)
                ->orWhere('author', 'like', $search);
        }

        if (!empty($filters['author'])) {
            $author = "%{$filters['author']}%";
            $query->where('author', 'like', $author);
        }

        // Фильтр по категории (жанру)
        if (!empty($filters['category'])) {
            $query->where('genre_id', $filters['category']);
        }

        // Диапазон цен
        if ($filters['min_price'] !== null) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if ($filters['max_price'] !== null) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // ДОБАВЛЕННЫЕ ФИЛЬТРЫ (по ТЗ, как у товаров)

        // 1. Остаток > 0 — для книг это: есть опубликованные главы
        $query->has('publishedChapters', '>', 0);

        // Загружаем связи, чтобы избежать N+1
        $books = $query->with([
            'seller',
            'seller.sellerPvz',
            'seller.contacts',
            'genre',
            'publishedChapters',
        ])->paginate($validPerPage);

        // Категории (жанры) для фильтров
        $categories = ProductGroup::whereHas('books', function ($q) {
            $q->active()->approved()->has('publishedChapters');
        })->distinct()
            ->orderBy('name')
            ->get();

        $FilterGroup = !empty($filters['category'])
            ? ProductGroup::find($filters['category'])
            : null;

        // --- SEO ---
        $titleParts = ['Книги'];

        if (!empty($filters['search'])) {
            $titleParts[] = 'по запросу «' . e($filters['search']) . '»';
        }

        if (!empty($FilterGroup)) {
            $titleParts[] = 'в категории «' . e($FilterGroup->name) . '»';
        }

        if (!empty($filters['type']) && $filters['type'] === 'ebook') {
            $titleParts[] = 'электронные книги';
        } elseif (!empty($filters['type']) && $filters['type'] === 'audiobook') {
            $titleParts[] = 'аудиокниги';
        }

        $title = implode(' ', $titleParts) . ' | Брауни Арт — книги и аудиокниги с доставкой по России';

        $descriptionParts = ['Широкий выбор книг и аудиокниг'];

        if (!empty($filters['search'])) {
            $descriptionParts[] = 'по запросу «' . e($filters['search']) . '»';
        }

        if (!empty($FilterGroup)) {
            $descriptionParts[] = 'в категории «' . e($FilterGroup->name) . '»';
        }

        $descriptionParts[] = 'Быстрая доставка по России. Гарантия качества. Актуальные цены.';
        $description = implode(' ', $descriptionParts);
        // --- Конец SEO ---

        return view('index', [
            'view'              => 'books.index',
            'shopMode'          => 1,
            'books'             => compact('books', 'filters', 'perPage'),
            'categories'        => $categories,
            'FilterGroup'       => $FilterGroup,
            'title'             => $title,
            'meta_description'  => $description,
        ]);
    }

    public function show(Request $request, $id){
        $this->shareCommonData($request);
        $book = Book::findOrFail($id);
        $view = 'books.show';
        return view('index', compact('book', 'view'));
    }
}
