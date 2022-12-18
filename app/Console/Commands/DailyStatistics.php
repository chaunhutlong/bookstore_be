<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Statistics;
use Illuminate\Support\Facades\DB;

class DailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Statistics for daily';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $from = date('Y-m-d 00:00:00');
            $to = date('Y-m-d 23:59:59');
            $orders = DB::table('orders')->whereBetween('created_at', [$from, $to])->get();
            $quantity = DB::table('orders_details')->join('orders', 'orders.id', '=', 'orders_details.order_id')->whereBetween('orders.created_at', [$from, $to])->get();
            $sale = DB::table('orders')->join('payments', 'payments.id', '=', 'orders.payment_id')->whereBetween('orders.created_at', [$from, $to])->get();
            $data = [
                'order_date' => date('Y-m-d'),
                'sales' => $sale->sum('total'),
                'quantity' => $quantity->sum('quantity'),
                'profit' => $sale->sum('total') * 0.25,
                'total_order' => $orders->count(),
            ];
            Statistics::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
