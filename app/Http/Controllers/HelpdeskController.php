<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Helpdesk;
use Illuminate\Support\Facades\Auth;
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
            log_event('helpdesk','Stworzenie zgłoszenia (order-id: '.$orderid.')');
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
            $discuss_class = "discuss discuss_change open";
            if($qry->helpdesk_status=="0")
                $discuss_class = "discuss discuss_change close";

            $row_id = $qry->helpdesk_id;
            $row = array("DT_RowId"=>'helpdesk_'.$row_id, 'DT_RowClass'=>'helpdesk_tr');
            $row[] = $j++;
            $row[] = 'Zgł do zam: '.'<a target="_blank" href="/order/add/'.$qry->order_id.'"><u>'.$qry->order_ident.'</u></a>';
            $row[] = $qry->c_date;
            $row[] = '<select style="max-width:80px;" class="form-control '.$discuss_class.'" data-ident="'.$row_id.'"><option value="0" '.(($qry->helpdesk_status=="0")?'selected':'').'>Zakończona</option><option value="1" '.(($qry->helpdesk_status=="1")?'selected':'').'>Otwarta</option></select>';
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
    public function change_status($id=NULL,$newStatus=0)
    {
        if(isset($id))
        {
            $insert_data = array(
                'e_date'=>date('Y-m-d H:i:s'),
                'e_by'=>((Auth::id())?Auth::id():'0'),
                'helpdesk_status'=>(string)$newStatus,
            );
            $QHLP = DB::table('helpdesk')->where(['helpdesk_id'=>$id])->get();
            if(count($QHLP)>0)
            {
                $Q = DB::table('orders')->select('orders.order_id','orders.order_ident','orders.products','client.*')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status'=>'1','order_id'=>$QHLP[0]->order_id])->get();
                if(count($Q)>0)
                {
                    DB::table('helpdesk')->where(['helpdesk_id'=>$id])->update($insert_data);
                    if($newStatus=="1")
                    {
                        $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Rozpoczęto / Wznowiono Państwa dyskusję do zamówienia: ".$Q[0]->order_ident."<br><br>Dyskusja dostępna pod następującym linkiem: <a href='http://127.0.0.1:8000/helpdesk/chat/".$QHLP[0]->helpdesk_id."/".md5($QHLP[0]->helpdesk_id.$QHLP[0]->c_date.$QHLP[0]->order_id.str_replace(array(1,2,3,4,5,6),array(9,5,2,8,4,3),$QHLP[0]->c_date))."'>Przejdź do dyskusji</a></td></tr>
            </table>
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";

                        send_mail($Q[0]->email,"miniErp - (Otwarto/Wznowiono) Dyskusję do zamówienia ".$Q[0]->order_ident,$BodyEmail);
                        log_event('helpdesk','Wznowienie dyskusji ('.$Q[0]->order_ident.')');
                    }
                    else
                    {
                        $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Zamknięto Państwa dyskusję do zamówienia: ".$Q[0]->order_ident."<br><br>Dyskusja dostępna pod następującym linkiem: <a href='http://127.0.0.1:8000/helpdesk/chat/".$QHLP[0]->helpdesk_id."/".md5($QHLP[0]->helpdesk_id.$QHLP[0]->c_date.$QHLP[0]->order_id.str_replace(array(1,2,3,4,5,6),array(9,5,2,8,4,3),$QHLP[0]->c_date))."'>Przejdź do dyskusji</a></td></tr>
            </table>
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";

                        send_mail($Q[0]->email,"miniErp - (Zamknięto) Dyskusję do zamówienia ".$Q[0]->order_ident,$BodyEmail);
                        log_event('helpdesk','Zamknięcie dyskusji ('.$Q[0]->order_ident.')');
                    }
                }
                else
                    return 0;
            }
        }
    }
    public function refresh_chat($id=NULL)
    {
        if(isset($id))
        {
            $check_Exist = DB::table('helpdesk_messages')->where(['helpdesk_id'=>$id])->get();
            if(count($check_Exist)>0)
            {
                $usrs = DB::table('users')->get();
                $usrs_arr = array();
                foreach ($usrs as $usr)
                    $usrs_arr[$usr->id] = $usr->name;

                $messages_content = '';
                foreach ($check_Exist as $ce)
                {
                    if($ce->c_by!='0')
                    {

                        $messages_content.='  <div class="message_block">
                                    <div class="message_right">
                                        <small>'.date('d/M/Y H:i:s',strtotime($ce->c_date)).((isset($usrs_arr[$ce->c_by]))?' ('.$usrs_arr[$ce->c_by].') ':'').'</small><br>
                                        '.$ce->message.'
                                    </div>
                                </div>';
                    }
                    else
                    {
                        $messages_content.='  <div class="message_block">
                                    <div class="message_left">
                                        <small>'.date('d/M/Y H:i:s',strtotime($ce->c_date)).'</small><br>
                                        '.$ce->message.'
                                    </div>
                                </div>';
                    }
                }
                echo $messages_content;
            }
            else
                echo 'Brak wiadomości';
        }
    }
    public function add_message($id=NULL)
    {
        if(isset($id) && isset($_POST['message']))
        {
            $insert_data = array(
                'helpdesk_id'=>$id,
                'message'=>$_POST['message'],
                'c_date'=>date('Y-m-d H:i:s'),
                'c_by'=>((Auth::id())?Auth::id():'0'),
                'status'=>'1',
            );
            DB::table('helpdesk_messages')->insert($insert_data);
        }
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
