<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class StaticPagesController extends BaseController
{
 public function aboutUs(Request $request){
     $this->shareCommonData($request); // вызываем один раз
     return view('index', ['view'=>'pages.static.aboutus', 'title'=>'О нас | BrauniArt маркетплейс']);
 }

 public function contacts(Request $request){
     $this->shareCommonData($request); // вызываем один раз
     $wareHouses = Warehouse::where('is_custom', true)->get();
     return view('index', ['view'=>'pages.static.contacts', 'title'=>'Контакты | BrauniArt маркетплейс', 'wareHouses'=>$wareHouses]);
 }

 public function PayAndDelivery(Request $request){
   $this->shareCommonData($request); // вызываем один раз
   return view('index', ['view'=>'pages.static.PayAndDelivery', 'title'=>'Оплата и доставка | BrauniArt маркетплейс']);
 }

 public function refunds(Request $request){
     $this->shareCommonData($request); // вызываем один раз
     return view('index', ['view'=>'pages.static.refunds', 'title'=>'Политика возвратов | BrauniArt маркетплейс']);
 }

}
