<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Request;

class NoteController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='note';
        $this->baseview='note';
    }
    public function index()
    {
        $this->middleware('auth');
        $this->middleware('hooks');
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
    public function get_list($page = 1)
    {
        $this->middleware('auth');
        $this->middleware('hooks');
        $request = Request::instance();
        $data = [];
        $j=1;

        $NoteModel = new Note;
        $query = $NoteModel->datatable_rows();


        foreach($query as $qry)
        {
            $Get_user_detail = DB::table('users')->where(['id'=>$qry->c_by])->get();
            $user_detail = '-';
            if(isset($Get_user_detail[0]))
                $user_detail = $Get_user_detail[0]->name;

            $mdl = 'black';
            switch ($qry->module)
            {
                case 'client':
                    $mdl = 'blue';
                    break;
                case 'order':
                    $mdl = 'red';
                    break;
                case 'product':
                    $mdl = 'orange';
                    break;
                case 'delivery':
                    $mdl = 'purple';
                    break;
                case 'helpdesk':
                    $mdl = 'pink';
                    break;
                case 'offer':
                    $mdl = 'green';
                    break;
            }

            $row_id = $qry->note_id;
            $row = array("DT_RowId"=>'helpdesk_'.$row_id, 'DT_RowClass'=>'helpdesk_tr');
            $row[] = $j++;
            $row[] = $qry->event;
            $row[] = '<span style="color: '.$mdl.'!important">'.$qry->module.'</span>';
            $row[] = $qry->c_date;
            $row[] = $user_detail;

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $NoteModel->count_all(),
            "recordsFiltered" => $NoteModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
}
