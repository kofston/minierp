<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Helpdesk;
use Request;

class HelpdeskController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='helpdesk';
        $this->baseview='helpdesk';
        $this->middleware('auth');
        $this->middleware('hooks');
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

        $HelpdeskModel = new Helpdesk;
        $query = $HelpdeskModel->datatable_rows();


        foreach($query as $qry)
        {
            $row_id = $qry->helpdesk_id;
            $row = array("DT_RowId"=>'helpdesk_'.$row_id, 'DT_RowClass'=>'helpdesk_tr');
            $row[] = $j++;
            $row[] = 'tu bedzie ticket';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/helpdesk/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/helpdesk/delete/'.$row_id.'">&#10006;</a></li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $HelpdeskModel->count_all(),
            "recordsFiltered" => $HelpdeskModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
}
