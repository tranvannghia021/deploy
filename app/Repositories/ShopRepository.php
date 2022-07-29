<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Repositories\BaseRepository;;

class ShopRepository extends BaseRepository
{
    protected $shop;
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        parent::__construct($shop);
    }
    public function getByDomain($domain)
    {
        
        $result = $this->shop->where('shopify_domain', $domain)->first();
        if (is_null($result)) {

            return null;
        }

        return $result;
    }
}
