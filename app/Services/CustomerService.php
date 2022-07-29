<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class CustomerService {

    //get all cus from shopify
    public function getAllCustomer($shop,$url=null){
        $response = Http::withHeaders([
            'X-Shopify-Access-Token'=>$shop->access_token
        ])->get(is_null($url) ? 'https://'.$shop->shopify_domain.'/admin/api/'.env('VERTION_API_SHOPIFY').'/customers.json?limit=250' : $url);
        
        return $response;
    }


    // count customer
    public function countCustomer($shop){
        $response = Http::withHeaders([
            'X-Shopify-Access-Token'=>$shop->access_token
        ])->get('https://'.$shop->shopify_domain.'/admin/api/'.env('VERTION_API_SHOPIFY').'/customers/count.json');

        return $response;
    }
}