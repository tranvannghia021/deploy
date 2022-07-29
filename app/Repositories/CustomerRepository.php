<?php

namespace App\Repositories;

use App\Models\Customer;

use App\Repositories\BaseRepository;
use LDAP\Result;

class CustomerRepository extends BaseRepository
{
    protected $customer;
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        parent::__construct($customer);
    }


    public function getAllCus($id)
    {
        
       return $this->customer->where('id_shops',$id)->get();
        
    }


    public function simplePaginate($limit=13,$id){
        
        return $this->model->where('id_shops',$id)->simplePaginate($limit);
    }


    public function search($keySearch, $page=1, $limit = 15,$id)
    {
        $page = $page < 0 ||is_null($page) ? 1 : $page;
        $limit = intval($limit);
        $customers = $this->getAllCus($id);
        $search = preg_quote($keySearch, '~');
        $filtered = $customers->filter(function ($value, $key) use ($search) {
          
            return preg_grep('~' . $search . '~i', $value->toArray());
        });

        return $filtered->forPage($page, $limit);
    }


    public function filter($datas,$page,$limit,$id){
        $customers=$this->getAllCus($id);;
        $result=null;
        if(!is_null($datas['startDay']) && !is_null($datas['endDay'])){
            $result=$customers->whereBetween('cus_created_at',[$datas['startDay'],$datas['endDay'].' 23:59:59']);
        }
        if(!is_null($datas['totalSpentStart']) && !is_null($datas['totalSpentEnd'])){
            
            $result=$customers->whereBetween('total_spent',[$datas['totalSpentStart'],$datas['totalSpentEnd']]);
        }
        if(!is_null($datas['totalOrderStart']) && !is_null($datas['totalOrderEnd'])){
           
            $result=$customers->whereBetween('total_order',[$datas['totalOrderStart'],$datas['totalOrderEnd']]);
        }

        return $result->forPage($page,$limit);
    }


    public function sortCustomer($limit=15,$sort='ASC',$id){

        return $this->customer->where('id_shops',$id)->orderBy('cus_created_at',$sort)->simplePaginate($limit);
    }


    public function findByid($id){
        return $this->customer->where('id_cus_shopify',$id)->first();
    }


    public function updateCus($id, array $datas)
    {
        try {

           $customer= $this->customer->where('id_cus_shopify',$id)->update($datas);
        } catch (\Exception $e) {
            return null;
        }
        return $customer;
    }


    public function delete($id)
    {
        try {
            $result = $this->customer->where('id_cus_shopify', $id)->delete();
            
        } catch (\Exception $e) {
            return false;
        }
        return true;
    
    }
}
