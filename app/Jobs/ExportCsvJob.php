<?php

namespace App\Jobs;

use App\Repositories\CustomerRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shops;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shops)
    {
        $this->shops=$shops;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $cusRepo)
    {
        $customers=$cusRepo->getAllCus($this->shops->id);
        $filename= fopen(storage_path('app/public/customer-csv/list-customer-'.date('Y-m-d-H:i:s').'.csv'),'w') or die('Permission error');
        foreach($customers as $cus){
             fputcsv($filename,(array)$cus->toArray());
        }
        $result=fclose($filename);
        if($result){
            return response()->json([
                'success'=>true,
                'message'=>'Sended mail successfully with file csv'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'Sended mail failed with file csv'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
