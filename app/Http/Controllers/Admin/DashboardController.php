<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\DeliveryMan;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Zone;
use App\Models\OrderTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];
        session()->put('dash_params', $params);
        $data = self::dashboard_data();
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        return view('admin-views.dashboard', compact('data', 'total_sell', 'commission', 'params'));
    }

    public function order(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'statistics_type') {
                $params['statistics_type'] = $request['statistics_type'];
            }
        }
        session()->put('dash_params', $params);

        if ($params['zone_id'] != 'all') {
            $restaurant_ids = Restaurant::where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
        } else {
            $restaurant_ids = Restaurant::pluck('id')->toArray();
        }
        $data = self::order_stats_calc(array_unique($restaurant_ids));
        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function zone(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'zone_id') {
                $params['zone_id'] = $request['zone_id'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_data();
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $popular = $data['popular'];
        $top_customer = $data['top_customer'];
        $top_deliveryman = $data['top_deliveryman'];
        $top_rated_foods = $data['top_rated_foods'];
        $top_restaurants = $data['top_restaurants'];
        $top_sell = $data['top_sell'];

        return response()->json([
            'popular_restaurants' => view('admin-views.partials._popular-restaurants', compact('popular'))->render(),
            'top_customer' => view('admin-views.partials._top-customer', compact('top_customer'))->render(),
            'top_deliveryman' => view('admin-views.partials._top-deliveryman', compact('top_deliveryman'))->render(),
            'top_rated_foods' => view('admin-views.partials._top-rated-foods', compact('top_rated_foods'))->render(),
            'top_restaurants' => view('admin-views.partials._top-restaurants', compact('top_restaurants'))->render(),
            'top_selling_foods' => view('admin-views.partials._top-selling-foods', compact('top_sell'))->render(),

            'order_stats' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render(),
            'user_overview' => view('admin-views.partials._user-overview-chart', compact('data'))->render(),
            'business_overview' => view('admin-views.partials._business-overview-chart', compact('data'))->render(),
            'monthly_graph' => view('admin-views.partials._monthly-earning-graph', compact('total_sell', 'commission'))->render(),
        ], 200);
    }

    public function user_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'user_overview') {
                $params['user_overview'] = $request['user_overview'];
            }
        }
        session()->put('dash_params', $params);
        //user overview
        if ($params['zone_id'] != 'all') {
            $restaurant_ids = Restaurant::where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
            $zone_ids = [$params['zone_id']];
            $user_ids = CustomerAddress::whereIn('zone_id', $zone_ids)->pluck('user_id')->toArray();
        } else {
            $zone_ids = Zone::pluck('id')->toArray();
            $restaurant_ids = Restaurant::pluck('id')->toArray();
            $user_ids = CustomerAddress::whereIn('zone_id', $zone_ids)->pluck('user_id')->toArray();
        }

        $data = self::user_overview_calc(array_unique($restaurant_ids), array_unique($zone_ids), array_unique($user_ids));

        return response()->json([
            'view' => view('admin-views.partials._user-overview-chart', compact('data'))->render()
        ], 200);
    }

    public function business_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'business_overview') {
                $params['business_overview'] = $request['business_overview'];
            }
        }
        session()->put('dash_params', $params);
        //business overview
        if ($params['zone_id'] != 'all') {
            $restaurant_ids = Restaurant::where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
        } else {
            $restaurant_ids = Restaurant::pluck('id')->toArray();
        }

        $data = self::business_overview_calc(array_unique($restaurant_ids));

        return response()->json([
            'view' => view('admin-views.partials._business-overview-chart', compact('data'))->render()
        ], 200);
    }

    public function order_stats_calc($restaurant_ids)
    {
        $params = session('dash_params');

        if ($params['statistics_type'] == 'today') {
            $searching_for_dm = Order::SearchingForDeliveryman()->whereDate('created_at', Carbon::now());
            $accepted_by_dm = Order::AccepteByDeliveryman()->whereDate('created_at', Carbon::now());
            $preparing_in_rs = Order::Preparing()->whereDate('created_at', Carbon::now());
            $picked_up = Order::FoodOnTheWay()->whereDate('created_at', Carbon::now());
            $delivered = Order::Delivered()->whereDate('delivered', Carbon::now());
            $canceled = Order::where(['order_status' => 'canceled'])->whereDate('canceled', Carbon::now());
            $refund_requested = Order::where(['order_status' => 'refund_requested'])->whereDate('refund_requested', Carbon::now());
            $refunded = Order::where(['order_status' => 'refunded'])->whereDate('refunded', Carbon::now());
        } else {
            $searching_for_dm = Order::SearchingForDeliveryman();
            $accepted_by_dm = Order::AccepteByDeliveryman();
            $preparing_in_rs = Order::Preparing();
            $picked_up = Order::FoodOnTheWay();
            $delivered = Order::Delivered();
            $canceled = Order::Canceled();
            $refund_requested = Order::failed();
            $refunded = Order::Refunded();
        }

        $searching_for_dm = $searching_for_dm->OrderScheduledIn(30)->whereIn('restaurant_id', $restaurant_ids)->count();
        $accepted_by_dm = $accepted_by_dm->whereIn('restaurant_id', $restaurant_ids)->count();
        $preparing_in_rs = $preparing_in_rs->whereIn('restaurant_id', $restaurant_ids)->count();
        $picked_up = $picked_up->whereIn('restaurant_id', $restaurant_ids)->count();
        $delivered = $delivered->whereIn('restaurant_id', $restaurant_ids)->count();
        $canceled = $canceled->whereIn('restaurant_id', $restaurant_ids)->count();
        $refund_requested = $refund_requested->whereIn('restaurant_id', $restaurant_ids)->count();
        $refunded = $refunded->whereIn('restaurant_id', $restaurant_ids)->count();

        $data = [
            'searching_for_dm' => $searching_for_dm,
            'accepted_by_dm' => $accepted_by_dm,
            'preparing_in_rs' => $preparing_in_rs,
            'picked_up' => $picked_up,
            'delivered' => $delivered,
            'canceled' => $canceled,
            'refund_requested' => $refund_requested,
            'refunded' => $refunded
        ];
        return $data;
    }

    public function user_overview_calc($restaurant_ids, $zone_ids, $user_ids)
    {
        $params = session('dash_params');
        //user overview
        if ($params['user_overview'] == 'overall') {
            $customer = User::whereIn('id', $user_ids)->count();
            $restaurants = Restaurant::whereIn('id', $restaurant_ids)->count();
            $delivery_man = DeliveryMan::whereIn('zone_id', $zone_ids)->count();
        } else {
            $customer = User::whereIn('id', $user_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $restaurants = Restaurant::whereIn('id', $restaurant_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $delivery_man = DeliveryMan::whereIn('zone_id', $zone_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
        }
        $data = [
            'customer' => $customer,
            'restaurants' => $restaurants,
            'delivery_man' => $delivery_man
        ];
        return $data;
    }

    public function business_overview_calc($restaurant_ids)
    {
        $params = session('dash_params');
        //business overview
        if ($params['business_overview'] == 'overall') {
            $foods = Food::whereIn('restaurant_id', $restaurant_ids)->count();
            $foods_ids = Food::whereIn('restaurant_id', $restaurant_ids)->pluck('id')->toArray();
            $reviews = Review::whereIn('food_id', $foods_ids)->count();
            $wishlist = Wishlist::whereIn('food_id', $foods_ids)->count();
        } else {
            $foods = Food::whereIn('restaurant_id', $restaurant_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $foods_ids = Food::whereIn('restaurant_id', $restaurant_ids)->pluck('id')->toArray();
            $reviews = Review::whereIn('food_id', $foods_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $wishlist = Wishlist::whereIn('food_id', $foods_ids)->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
        }

        $data['food'] = $foods;
        $data['reviews'] = $reviews;
        $data['wishlist'] = $wishlist;

        return $data;
    }

    public function dashboard_data()
    {
        $params = session('dash_params');
        if ($params['zone_id'] != 'all') {
            $restaurant_ids = Restaurant::where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
            $zone_ids = [$params['zone_id']];
            $user_ids = CustomerAddress::whereIn('zone_id', $zone_ids)->pluck('user_id')->toArray();
            $vendor_ids = Restaurant::whereIn('id', $restaurant_ids)->pluck('vendor_id')->toArray();
            $food_ids = Food::whereIn('restaurant_id', $restaurant_ids)->pluck('id')->toArray();
        } else {
            $zone_ids = Zone::pluck('id')->toArray();
            $restaurant_ids = Restaurant::pluck('id')->toArray();
            $user_ids = CustomerAddress::whereIn('zone_id', $zone_ids)->pluck('user_id')->toArray();
            $vendor_ids = Restaurant::pluck('vendor_id')->toArray();
            $food_ids = Food::pluck('id')->toArray();
        }
        $data_os = self::order_stats_calc(array_unique($restaurant_ids));
        $data_uo = self::user_overview_calc(array_unique($restaurant_ids), array_unique($zone_ids), array_unique($user_ids));
        $data_bo = self::business_overview_calc(array_unique($restaurant_ids));

        $popular = Wishlist::with(['restaurant'])->whereIn('restaurant_id', $restaurant_ids)->select('restaurant_id', DB::raw('COUNT(restaurant_id) as count'))->groupBy('restaurant_id')->orderBy('count', 'DESC')->limit(6)->get();
        $top_sell = OrderDetail::with(['food'])->whereIn('food_id', $food_ids)
            ->select('food_id', DB::raw('COUNT(food_id) as count'))
            ->groupBy('food_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();
        $top_rated_foods = Food::rightJoin('reviews', 'reviews.food_id', '=', 'food.id')
            ->whereIn('food_id', $food_ids)
            ->groupBy('food_id')
            ->select(['food_id',
                DB::raw('AVG(reviews.rating) as ratings_average'),
                DB::raw('count(*) as total')
            ])
            ->orderBy('total', 'desc')
            ->take(6)
            ->get();
        $top_deliveryman = Order::with(['delivery_man'])->whereIn('restaurant_id', $restaurant_ids)
            ->select('delivery_man_id', DB::raw('COUNT(delivery_man_id) as count'))
            ->groupBy('delivery_man_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();

        $top_customer = Order::with(['customer'])->whereIn('restaurant_id', $restaurant_ids)
            ->notpos()
            ->select('user_id', DB::raw('COUNT(user_id) as count'))
            ->groupBy('user_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();

        $top_restaurants = Order::with(['restaurant'])->Delivered()
            ->whereIn('restaurant_id', $restaurant_ids)
            ->select('restaurant_id', DB::raw('COUNT(restaurant_id) as count'))
            ->groupBy('restaurant_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();

        $total_sell = [];
        $commission = [];
        for ($i = 1; $i <= 12; $i++) {
            $from = date('Y-' . $i . '-01');
            $to = date('Y-' . $i . '-30');
            $total_sell[$i] = OrderTransaction::NotRefunded()
                ->whereIn('vendor_id', $vendor_ids)
                ->whereBetween('created_at', [$from, $to])->sum('order_amount');
            $commission[$i] = OrderTransaction::NotRefunded()
                ->whereIn('vendor_id', $vendor_ids)
                ->whereBetween('created_at', [$from, $to])->sum('admin_commission');
        }

        $dash_data = array_merge($data_os, $data_uo, $data_bo);
        $dash_data['popular'] = $popular;
        $dash_data['top_sell'] = $top_sell;
        $dash_data['top_rated_foods'] = $top_rated_foods;
        $dash_data['top_deliveryman'] = $top_deliveryman;
        $dash_data['top_customer'] = $top_customer;
        $dash_data['top_restaurants'] = $top_restaurants;
        $dash_data['total_sell'] = $total_sell;
        $dash_data['commission'] = $commission;

        return $dash_data;
    }
}
