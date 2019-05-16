<?php
/**
 * Save coupon data
 */
namespace Modules\Backend\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Backend\Core\AppModel;
use Modules\Backend\Models\Order;
use Modules\Backend\Models\Product;
use Modules\Backend\Models\User;

class Dashboard extends Model
{
    /**
     * Get total order
     */
    public static function getOrderTotal()
    {
        $data = Order::select('total')->get();
        $total = 0;
        if (!empty($data)) {
            foreach ($data as $row) {
                $total += $row->total;
            }
        }
        return [
            'count' => count($data),
            'total' => (float) $total
        ];
    }

    /**
     * Count customer
     */
    public static function countCustomer()
    {
        $data = User::count();
        return $data;
    }

    /**
     * Count product
     */
    public static function countProduct()
    {
        $data = Product::count();
        return $data;
    }

    /**
     * Assign total for each day in current week
     */
    public static function assignTotalForEachDayInCurrentWeek($monday, $order)
    {
        //$tuesday = $monday->copy()->addDays(1);
        $dayOfCurrentWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $dayOfCurrentWeek[] = $monday->copy()->addDays($i)->toDateString();
        }
        $rs = [];
        foreach ($dayOfCurrentWeek as $day) {
            $rs[$day] = 0;
            if (array_key_exists($day, $order)) {
                $total = 0;
                foreach ($order[$day] as $sale) {
                    $total += $sale;
                }
                $rs[$day] = $total;
            }
        }
        return array_values($rs);
    }

    /**
     * Get order by current week
     */
    public static function getOrderByCurrentWeek()
    {
        $monday = Carbon::now()->startOfWeek();
        $sunday = Carbon::now()->endOfWeek();
        $data = Order::select('total', 'created_at')
            ->whereDate('created_at', '>=', $monday)
            ->whereDate('created_at', '<=', $sunday)
            ->get();
        $order = [];
        if (!empty($data)) {
            foreach ($data as $row) {
                $dateArray = explode(' ', $row->created_at);
                $order[$dateArray[0]][] = $row->total;
            }
        }
        return self::assignTotalForEachDayInCurrentWeek($monday, $order);
    }

    /**
     * Get latest order
     */
    public static function getLatestOrder()
    {
        $data = Order::select('id', 'order_status_id', 'billing_first_name', 'billing_last_name', 'total')
            ->with(['orderStatus'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        return $data;
    }

    /**
     * Get dashboard data
     */
    public static function getDashboardData()
    {
        $cacheKey = 'dashboard';
        $rs = AppModel::returnCacheData($cacheKey, function () {
            $data['order'] = self::getOrderTotal();
            $data['customerCount'] = self::countCustomer();
            $data['productCount'] = self::countProduct();
            $data['orderByCurrentWeek'] = self::getOrderByCurrentWeek();
            $data['latestOrder'] = self::getLatestOrder();
            return $data;
        });
        return $rs;
    }
}