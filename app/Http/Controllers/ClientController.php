<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $ClientModel = new Client;
        $query = $ClientModel->get_details($id);
        return view($this->module."/".$this->baseview,['query'=>json_encode($query)]);
    }
    public function save($id=NULL)
    {
        $insert_data = array(
            'name'=>$_POST['name'],
            'symbol'=>$_POST['symbol'],
            'street'=>$_POST['street'],
            'building'=>$_POST['building'],
            'alt'=>$_POST['alt'],
            'zip'=>$_POST['zip'],
            'city'=>$_POST['city'],
            'country'=>$_POST['country'],
            'email'=>$_POST['email'],
            'phone'=>$_POST['phone'],
            'e_by'=>((Auth::id())?Auth::id():'0'),
        );

        if(isset($id) && $id!='')
        {
            $insert_data['e_date'] = date('Y-m-d H:i:s');
            DB::table('client')->where(['client_id'=>$id])->update($insert_data);
            return redirect('/client?msg=success_updated');
        }
        else
        {
            $count_all = DB::table('client')->count();
           $insert_data['symbol'] = $count_all."_".time();
            DB::table('client')->insert($insert_data);
            return redirect('/client?msg=success_insert');
        }

    }
    public function delete($id=NULL)
    {
        if(isset($id))
            DB::table('client')->where(['client_id'=>$id])->update(['status'=>'0','e_date'=>date('Y-m-d H:i:s'),'e_by'=>((Auth::id())?Auth::id():'0')]);
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
            $row[] = $qry->e_date.(($qry->e_by!='0')?'<br>':'');
            $row[] = '<ul><li><a title="Edytuj" href="/client/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext delete_row" title="Usuń" data-module="client" data-id="'.$row_id.'" href="javascript:void(0);">&#10006;</a></li></ul>';

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
