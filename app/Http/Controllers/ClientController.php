<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Client;
use Request;

class ClientController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='client';
        $this->baseview='client';
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

        $ClientModel = new Client;
        $query = $ClientModel->datatable_rows();


        foreach($query as $qry)
        {
            $row_id = $qry->client_id;
            $row = array("DT_RowId"=>'client_'.$row_id, 'DT_RowClass'=>'client_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/client/add/'.$row_id.'">'.$qry->name.'</a></div>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/client/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/client/delete/'.$row_id.'">&#10006;</a></li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $ClientModel->count_all(),
            "recordsFiltered" => $ClientModel->count_filtered(),
            "data" => $data,
        );

            echo json_encode($output);

    }
}
