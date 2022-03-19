<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Request;

class OrderController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='order';
        $this->baseview='order';
        $this->middleware('auth');
        $this->middleware('hooks');
    }
    public function index()
    {
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
    public function add($id=NULL)
    {
        $this->baseview = 'add';
        checkAccess();
        $OrderModel = new Order;
        $query = $OrderModel->get_details($id);
        $clients_array = file_get_contents('cache/clients.php');
        $products_array = file_get_contents('cache/products.php');
        return view($this->module."/".$this->baseview,['query'=>json_encode($query),'clients_array'=>$clients_array,'products_array'=>$products_array]);
    }
    public function save($id=NULL)
    {

        $insert_data = array(
            'products'=>json_encode($_POST['PRODUCT']),
            'client_id'=>$_POST['client'],
            'delivery_id'=>$_POST['delivery'],
            'order_status'=>'0',
            'order_date'=>$_POST['order_date'],
            'c_date'=>date('Y-m-d H:i:s'),
            'e_by'=>((Auth::id())?Auth::id():'0'),
            'status'=>'1',
        );

        if(isset($id) && $id!='')
        {
            $insert_data['e_date'] = date('Y-m-d H:i:s');
            DB::table('orders')->where(['order_id'=>$id])->update($insert_data);
            return redirect('/order?msg=success_updated');
        }
        else
        {
            $count_all = DB::table('orders')->count();
            $insert_data['order_ident'] = 'ZA/'.($count_all+1).'/'.date('m/Y');
            DB::table('orders')->insert($insert_data);
            return redirect('/order?msg=success_insert');
        }

    }
    public function get_list($page = 1)
    {
        $request = Request::instance();
        $data = [];
        $j=1;

        $OrderModel = new Order;
        $query = $OrderModel->datatable_rows();


        foreach($query as $qry)
        {
            $product_list = '';
            $product_list_q = json_decode($qry->products);

            for ($i=0;$i<count((array)$product_list_q->name);$i++)
            {
                $product_list.= '<li>'.$product_list_q->name[$i].'</li>';
            }

            $row_id = $qry->order_id;
            $row = array("DT_RowId"=>'order_'.$row_id, 'DT_RowClass'=>'order_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/order/add/'.$row_id.'">'.$qry->order_ident.'</a></div>';
            $row[] = '<ul class="m-0" style="font-size: 14px;display: block;list-style: disc;">'.$product_list.'</ul>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/order/add/'.$row_id.'">&#9998;</a></li><li>Status tutaj</li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $OrderModel->count_all(),
            "recordsFiltered" => $OrderModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
}
