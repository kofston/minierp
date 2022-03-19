<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HooksMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //Stwórz hooksy (czas trwania 2h (120min))

            //Klienci
            $client_cache = 'cache/clients.php';
            if (file_exists($client_cache) && (filemtime($client_cache) > (time() - 60 * 120 ))) {

            } else {
                $clients = DB::table('client')->get();
                $clients_array_cache = array();
                    foreach ($clients as $clt)
                    {
                        $clients_array_cache[$clt->client_id] = $clt;
                    }
                $file_clients = json_encode($clients_array_cache);
                file_put_contents($client_cache, $file_clients, LOCK_EX);
            }

            //Produkty
            $product_cache = 'cache/products.php';
            if (file_exists($product_cache) && (filemtime($product_cache) > (time() - 60 * 120 ))) {

            } else {
                $products = DB::table('product')->get();
                $products_array_cache = array();
                foreach ($products as $prd)
                {
                    $products_array_cache[$prd->product_id] = $prd;
                }
                $file_products= json_encode($products_array_cache);
                file_put_contents($product_cache, $file_products, LOCK_EX);
            }

        //

        return $next($request);
    }
}
