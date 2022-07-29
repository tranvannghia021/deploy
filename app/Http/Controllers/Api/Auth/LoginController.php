<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\LoginEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shop\ShopResource;
use App\Repositories\ShopRepository;
use App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    protected $shopService;
    protected $shopRepos;
    public function __construct(ShopService $shopService, ShopRepository $shopRepos)
    {
        $this->shopService = $shopService;
        $this->shopRepos = $shopRepos;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $name = $request->domain;
     
   
        $response = $this->shopService->isCheckShop($name);
        // thông báo khi không có
        if ($response == Response::HTTP_NOT_FOUND) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found!'
            ], Response::HTTP_NOT_FOUND);
        } else {
            // Lấy hmac và code 
            return response()->json([
                'success'=>true,
                'message'=>'Link redirect',
                'link'=>'https://' . $name . '.myshopify.com/admin/oauth/authorize?client_id=' . env('API_KEY_SHOPIFY_APP') . '&scope=' . env('SCOPE_SHOPIFY') . '&redirect_uri=' . env('REDIRECT_URL')
            ]);
        }
    }




    public function loginShopify(Request $request)
    {
        $domain = $request->shop;
        $code = $request->code;

        $checkHmac = $this->verifyHmac($request);

        if ($checkHmac) {

            $dataShops = $this->shopRepos->getByDomain($domain);
            if (is_null($dataShops)) {
                // Lấy access_token

                $accessToken = $this->shopService->getAccessToken($domain, $code);
                $responseShop = $this->shopService->getInfoShop($accessToken, $domain);
                $infoShop = $responseShop->json('shop');

                $result = $this->shopRepos->create(
                    [
                        'id' => $infoShop['id'],
                        'name' => $infoShop['name'],
                        'email' => $infoShop['email'],
                        'shopify_domain' => $infoShop['domain'],
                        'hash_domain' => Hash::make($infoShop['domain']),
                        'access_token' => $accessToken
                    ]
                );
                if (!is_null($result)) {
                    event(new LoginEvent($result));
                    $token = $this->createToken($result);
                    return $this->responseLogin($result, $token);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login failed !!',
                        'data' => []
                    ], 401);
                }
            } else {
                
                $token =$this->createToken($dataShops);
                return $this->responseLogin($dataShops, $token);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login is failed,you already have an account Shopify',
                'data' => []
            ], 422);
        }
    }


    protected function verifyHmac($request)
    {
        $query = http_build_query([
            'code' => $request->code,
            'host' => $request->host,
            'shop' => $request->shop,
            'timestamp' => $request->timestamp
        ]);

        $hmacShopify = $request->hmac;

        $hmacApp = hash_hmac('sha256', $query, env('API_SECRET_KEY_SHOPIFY_APP'));

        if ($hmacApp === $hmacShopify) {
            return true;
        }
        return false;
    }


    public function responseLogin($dataShops, $token)
    {
        
        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expried_in' => auth()->factory()->getTTL() * 60,
            'data' => []
        ]);
    }


    public function refresh(Request $request)
    {
        try {
            $shops= JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                $newToken = JWTAuth::parseToken()->refresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Token new',
                    'access_token' => $newToken,
                    'token_type' => 'bearer',
                    'expried_in' => auth()->factory()->getTTL() * 60,
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }


    public function me(){
        $shops=auth()->user();
        
        return response()->json([
            'success'=>true,
            'message'=>'Info shops',
            'data'=>new ShopResource($shops),
        ]);
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'success'=>true,
            'message'=>'Logout successfully'
        ]);
    }


    protected function createToken($shops){
        return auth()->attempt([
            'id' => $shops->id,
            'name' => $shops->name,
            'shopify_domain'=>$shops->shopify_domain,
            'password' => $shops->shopify_domain
        ]);
    }
}
