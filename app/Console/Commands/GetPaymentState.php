<?php

namespace App\Console\Commands;

use App\Http\Integrations\TBank\Requests\GetState;
use App\Http\Integrations\TBank\TbankConnector;
use App\Models\Order;
use App\Services\TbankService;
use Illuminate\Console\Command;

class GetPaymentState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-payment-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status', 'pending')->get();
        foreach ($orders as $order) {
            $connector = new TbankConnector();
            $request = new GetState($order->paymentId);
            $result = $connector->send($request)->json();
            if($result['Status'] == 'CONFIRMED') {
                $order->status = 'paid';
                $order->save();
            }
        }
    }
}
