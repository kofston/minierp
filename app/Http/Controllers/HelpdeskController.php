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
    }
    public function index()
    {
        $this->middleware('auth');
        $this->middleware('hooks');
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
    public function createticket($orderid=NULL)
    {
        $this->middleware('auth');
        $this->middleware('hooks');
        $check_Exist = DB::table('helpdesk')->where(['order_id'=>$orderid])->get();
        if(count($check_Exist)==0)
        {
            $insert_data = array(
                'order_id'=>$orderid,
                'c_date'=>date('Y-m-d H:i:s'),
                'helpdesk_status'=>'0',
                'status'=>'1',
            );
            DB::table('helpdesk')->insert($insert_data);
        }
        return redirect('/helpdesk');
    }
    public function get_list($page = 1)
    {
        $this->middleware('auth');
        $this->middleware('hooks');
        $request = Request::instance();
        $data = [];
        $j=1;

        $HelpdeskModel = new Helpdesk;
        $query = $HelpdeskModel->datatable_rows();


        foreach($query as $qry)
        {
            $discuss_class = "discuss open";
            if($qry->helpdesk_status=="0")
                $discuss_class = "discuss close";

            $row_id = $qry->helpdesk_id;
            $row = array("DT_RowId"=>'helpdesk_'.$row_id, 'DT_RowClass'=>'helpdesk_tr');
            $row[] = $j++;
            $row[] = 'Zgł do zam: '.$qry->order_ident;
            $row[] = $qry->c_date;
            $row[] = '<select style="max-width:80px;" class="form-control '.$discuss_class.'"><option value="0" '.(($qry->helpdesk_status=="0")?'selected':'').'>Zakończona</option><option value="1" '.(($qry->helpdesk_status=="1")?'selected':'').'>Otwarta</option></select>';
            $row[] = '<ul><li><a title="Edytuj" href="/helpdesk/chat/'.$row_id.'/'.md5($row_id.$qry->c_date.$qry->order_id.str_replace(array(1,2,3,4,5,6),array(9,5,2,8,4,3),$qry->c_date)).'">&#9998;</a></li></ul>';

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
    public function chat($helpdesk_id = NULL,$hash='')
    {
        if(isset($helpdesk_id))
        {
            $check_Exist = DB::table('helpdesk')->where(['helpdesk_id'=>$helpdesk_id])->get();
            if(count($check_Exist)>0)
            {
                $qry = $check_Exist[0];
                if($hash == md5($qry->helpdesk_id.$qry->c_date.$qry->order_id.str_replace(array(1,2,3,4,5,6),array(9,5,2,8,4,3),$qry->c_date)))
                {
                    $this->baseview = 'chat';
                    $HelpdeskModel = new Helpdesk;
                    $query = $HelpdeskModel->get_details($qry->helpdesk_id);
                    return view($this->module."/".$this->baseview,['query'=>json_encode($query)]);
                }
                else
                    return view($this->module."/error");
            }
            else
                return view($this->module."/error");
        }
        else
            return view($this->module."/error");
    }
}
