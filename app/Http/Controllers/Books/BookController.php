<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\BaseController;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends BaseController
{
    public function index(){

    }

    public function show(Request $request, $id){
        $this->shareCommonData($request);
        $book = Book::findOrFail($id);
        $view = 'books.show';
        return view('index', compact('book', 'view'));
    }
}
