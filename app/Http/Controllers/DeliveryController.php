<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Delivery;
use Request;

class DeliveryController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='delivery';
        $this->baseview='delivery';
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

        $DeliveryModel = new Delivery;
        $query = $DeliveryModel->datatable_rows();


        foreach($query as $qry)
        {
            $row_id = $qry->delivery_id;
            $row = array("DT_RowId"=>'delivery_'.$row_id, 'DT_RowClass'=>'delivery_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/delivery/add/'.$row_id.'">'.$qry->name.'</a></div>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/delivery/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/delivery/delete/'.$row_id.'">&#10006;</a></li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $DeliveryModel->count_all(),
            "recordsFiltered" => $DeliveryModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
}
