<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\ExportCsvJob;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $cusRepo;
    public function __construct(CustomerRepository $cusRepo)
    {
        $this->cusRepo=$cusRepo;   
    }
    public function exportCSV(Request $request){
        $shops=auth()->user();
        dispatch(new ExportCsvJob($shops));
        return response()->json([
            'success'=>true,
            'message'=>'the process is running',
        ]);
    }
}
