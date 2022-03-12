<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Order;
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
    }
    public function index()
    {
        checkAccess();
        return view($this->module."/".$this->baseview);
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
            $row_id = $qry->order_id;
            $row = array("DT_RowId"=>'order_'.$row_id, 'DT_RowClass'=>'order_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/order/add/'.$row_id.'">'.$qry->name.'</a></div>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/order/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/order/delete/'.$row_id.'">&#10006;</a></li></ul>';

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
