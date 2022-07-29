<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\CreateWebHookJob;
use App\Jobs\DeleteWebHookJob;
use App\Jobs\UpdateWebHookJob;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;

class WebHookController extends Controller
{
    protected $shopRepo;
    public function __construct(ShopRepository $shopRepo,Request $request )
    {
        $this->shopRepo=$shopRepo->getByDomain($request->header('X-Shopify-Shop-Domain'));
    }


    public function createWebHook(Request $request){
        if(is_null($this->shopRepo)){

            return response()->json([],200);
        }
        dispatch(new CreateWebHookJob($request->toArray(),$this->shopRepo));

        return response()->json([],200);
    }


    public function updateWebHook(Request $request){
        if(is_null($this->shopRepo)){

            return response()->json([],200);
        }
        dispatch(new UpdateWebHookJob($request->toArray(),$this->shopRepo));
        return response()->json([],200);

    }


    public function deleteWebHook(Request $request){
        if(is_null($this->shopRepo)){

            return response()->json([],200);
        }
        dispatch(new DeleteWebHookJob($request->toArray(),$this->shopRepo));
        return response()->json([],200);

    }
}
