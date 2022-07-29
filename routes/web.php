<?php

use App\Events\SaveCustomerEvent;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    dd($request->all());
    return view('welcome');
});
Route::get('/csv',function(){
    $cus=Customer::all();
    $filename= fopen(storage_path('/file.csv'),'w');
  
    foreach($cus as $c){
        // dd($c->toArray());
        fputcsv($filename,(array)$c->toArray());

    }
    fclose($filename);
});
Route::any('/test',function(Request $request){
    $response = Http::withHeaders([
        'X-Shopify-Access-Token'=>'shpua_55864873d34c3b16c75a8292553f1dd0'
    ])->get('https://token12.myshopify.com/admin/api/'.env('VERTION_API_SHOPIFY').'/customers.json?limit=3');
        //dd($response->header('link'));
    $total = Http::withHeaders([
        'X-Shopify-Access-Token'=>'shpua_55864873d34c3b16c75a8292553f1dd0'
    ])->get('https://token12.myshopify.com/admin/api/'.env('VERTION_API_SHOPIFY').'/customers/count.json');
        // dd(trim($response->header('link'),'<>; rel="next"'));
        dd(strpos($response->header('link'),'rel="next"'));
    $datas=$response->json('customers');
        
    $i=1;
    foreach($datas as $data){
        // dd($total->json('count'));
      // event(new SaveCustomerEvent($total->json('count') ,$i++));

    }
});
Route::any('a',function(){
   $string='2022-07-25T15:43:40+07:00';
//    $dt = new DateTime('2022-07-25T15:43:40+07:00', new DateTimeZone('UTC'));
//    $dt->setTimezone(new DateTimeZone('America/Denver'));
  
    $dateInLocal = date("Y-m-d H:i:s",strtotime($string.' UTC') );

  dd( $dateInLocal);

});
