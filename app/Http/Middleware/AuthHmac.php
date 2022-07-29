<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthHmac
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       // if(! app()->environment('local')){
        $rawBody=file_get_contents('php://input');
        if($this->verifyShopifyHmac($rawBody,env('API_SECRET_KEY_SHOPIFY_APP'),$request->header('X-Shopify-Hmac-SHA256'))){
          
           return $next($request);
        }else{

            return response()->json([
                'success'=>false,
                'message'=>'Insufficient permissions!',
            ],JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        }
 // }
    }


    private function verifyShopifyHmac($body, $secret, $hmac)
    {

        return hash_equals(base64_encode(hash_hmac('sha256', $body, $secret, true)), $hmac);
    }



}
