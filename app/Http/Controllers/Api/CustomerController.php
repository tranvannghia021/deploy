<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $cusRepo;
    public function __construct(CustomerRepository $cusRepo)
    {
        $this->cusRepo = $cusRepo;
    }


    public function getCustomer(Request $request)
    {
        $shops=auth()->user();
        $type = null;
        $type = $request->type;
        $page=( $request->page <= 0 || is_null($request->page)) ?( 1) :( $request->page );
        $urlPrev = $request->url() . '?page=' .$page - 1 . '&limit=' . $request->limit;
        $urlNext = $request->url() . '?page=' . $page + 1  . '&limit=' . $request->limit;
        $limit = is_null($request->limit) ? 15 : $request->limit;
        switch ($type) {
            case 'search':
                $keySearch=is_null($request->q)?null: $request->q;
                $urlPrev.='&type=search&q='.$keySearch ;
                $urlNext.='&type=search&q='.$keySearch ;
                
                $customers = $this->cusRepo->search($keySearch, $page, $limit,$shops->id);
                $datas = $customers->toArray();
                $countNumber = count($datas);
                if ($countNumber < $limit) {
                    $urlNext = null;
                }

                return $this->responseCustomer($customers,$urlPrev,$urlNext,$limit,$page);
               
                break;
            case 'filter':
                $urlPrev.='&type=filter';
                $urlNext.='&type=filter';
                $datas=[
                    'startDay'=>$request->start_day,
                    'endDay'=>$request->end_day,
                    'totalSpentStart'=>$request->total_spent_start,
                    'totalSpentEnd'=>$request->total_spent_end,
                    'totalOrderStart'=>$request->total_order_start,
                    'totalOrderEnd'=>$request->total_order_end,

                ];
               
                $customers=$this->cusRepo->filter($datas,$page,$limit,$shops->id);
                $datas = $customers->toArray();
                $countNumber = count($datas);
                if ($countNumber < $limit) {
                    $urlNext = null;
                }
                return $this->responseCustomer($customers,$urlPrev,$urlNext,$limit,$page);
                
                break;
            case 'sort':
                $sortDay=$request->sort_day;
                $urlPrev.='&type=sort';
                $urlNext.='&type=sort';
                $customers = $this->cusRepo->sortCustomer($limit,$sortDay,$shops->id);
                $datas = $customers->toArray();
                $countNumber = count($datas);
                if ($countNumber < $limit) {
                    $urlNext = null;
                }
                return  $this->responseCustomer($customers,$urlPrev,$urlNext,$limit);
                break;
            default:
                $customers = $this->cusRepo->simplePaginate($limit,$shops->id);
               
                return  $this->responseCustomer($customers,null,null,$limit);
        }
    }


    public function responseCustomer($customers, $urlPrev = null, $urlNext = null, $limit = null,$curentPage=null)
    {
        $datas = $customers->toArray();

        return response()->json(
            [
                'success' => true,
                'message' => 'list customers',
                'data' => CustomerResource::collection($customers),
                'current_page' =>is_null($curentPage)?  $datas['current_page'] :$curentPage,
                'prev_page_url' => is_null($urlPrev) ? (is_null($datas['prev_page_url']) ? null : $datas['prev_page_url'] . '&limit=' . $limit) : $urlPrev,
                'next_page_url' => is_null($urlNext) ? (empty($datas['next_page_url']) ? null : $datas['next_page_url'] . '&limit=' . $limit) : $urlNext,

            ]
        );
    }
}
