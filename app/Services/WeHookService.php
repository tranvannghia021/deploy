<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeHookService {

    
    public function register($shops){
        $arrayCreate=explode(',',env('TOPIC_SHOPIFY_CREATE'));
        $arrayUpdate=explode(',',env('TOPIC_SHOPIFY_UPDATE'));
        $arrayDelete=explode(',',env('TOPIC_SHOPIFY_DELETE'));

        foreach($arrayCreate as $topic){

            Http::withHeaders([
               'X-Shopify-Access-Token' => $shops->access_token,
               'Content-Type' => 'application/json'
           ])->post('https://' . $shops->shopify_domain . '/admin/api/'.env('VERTION_API_SHOPIFY').'/webhooks.json', [
               'webhook' => [
                   'topic' => $topic,
                   'address' => ''.env('DOMAIN_NGORK').'/api/webhook/customer/create',
                   'format' => 'json'
   
               ]
           ]);
        }
        foreach($arrayUpdate as $topic){
            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/'.env('VERTION_API_SHOPIFY').'/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => ''.env('DOMAIN_NGORK').'/api/webhook/customer/update',
                    'format' => 'json'
    
                ]
            ]);
        }
        foreach($arrayDelete as $topic){
            Http::withHeaders([
                'X-Shopify-Access-Token' => $shops->access_token,
                'Content-Type' => 'application/json'
            ])->post('https://' . $shops->shopify_domain . '/admin/api/'.env('VERTION_API_SHOPIFY').'/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'address' => ''.env('DOMAIN_NGORK').'/api/webhook/customer/delete',
                    'format' => 'json'
    
                ]
            ]);
        }
    }
}