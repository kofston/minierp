<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use Request;

class OfferController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='offer';
        $this->baseview='offer';
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
        $OfferModel = new Offer;
        $query = $OfferModel->get_details($id);
        $clients_array = file_get_contents('cache/clients.php');
        $products_array = file_get_contents('cache/products.php');
        return view($this->module."/".$this->baseview,['query'=>json_encode($query),'clients_array'=>$clients_array,'products_array'=>$products_array]);
    }
    public function save($id=NULL)
    {
        $insert_data = array(
            'message'=>$_POST['message'],
            'clients'=>json_encode($_POST['CLIENT']),
            'c_date'=>date('Y-m-d H:i:s'),
            'e_by'=>((Auth::id())?Auth::id():'0'),
            'status'=>'1',
        );

        if(isset($id) && $id!='')
        {
            $insert_data['e_date'] = date('Y-m-d H:i:s');
            DB::table('offer')->where(['offer_id'=>$id])->update($insert_data);
            log_event('offer','Edycja oferty (id'.$id.')');
            return redirect('/offer?msg=success_updated');
        }
        else
        {
            DB::table('offer')->insert($insert_data);
            log_event('offer','Zapisanie oferty (id'.$id.')');
            return redirect('/offer?msg=success_insert');
        }

    }
    public function get_list($page = 1)
    {
        $request = Request::instance();
        $data = [];
        $j=1;

        $OfferModel = new Offer;
        $query = $OfferModel->datatable_rows();

        $clients_array = json_decode(file_get_contents('cache/clients.php'));
        foreach($query as $qry)
        {
            if($qry->send=='1')
                $color_class = "green";
            else
                $color_class = "black";

            $client_list = '';
            foreach (json_decode($qry->clients) as $clt)
                $client_list.='<li>'.((isset($clients_array->{$clt}->name))?$clients_array->{$clt}->name:'').'</li>';

            $row_id = $qry->offer_id;
            $row = array("DT_RowId"=>'offer_'.$row_id, 'DT_RowClass'=>'offer_tr');
            $row[] = $j++;
            $row[] = '<div>'.$qry->message.'</div>';
            $row[] = '<ul class="m-0" style="list-style: disc;display:block;">'.$client_list.'</ul>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Wyślij" style="color: '.$color_class.'!important" href="/offer/send/'.$row_id.'">&#9745;</a></li><li><a title="Edytuj" href="/offer/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" data-module="offer" data-id="'.$row_id.'" title="Usuń" href="/offer/delete/'.$row_id.'">&#10006;</a></li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $OfferModel->count_all(),
            "recordsFiltered" => $OfferModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
    public function delete($id=NULL)
    {
        if(isset($id))
        {
            DB::table('offer')->where(['offer_id'=>$id])->update(['status'=>'0','e_date'=>date('Y-m-d H:i:s'),'e_by'=>((Auth::id())?Auth::id():'0')]);
            log_event('offer','Usunięcie oferty (id'.$id.')');
        }

        return redirect('/offer');
    }
    public function send($id=NULL)
    {
        if(isset($id))
        {
            $OfferModel = new Offer;
            $query = $OfferModel->get_details($id);
            if(count($query)>0)
            {
                $clients_array = json_decode(file_get_contents('cache/clients.php'));
                $clients = json_decode($query[0]->clients);
                foreach ($clients as $clt)
                {
                    $mail = $clients_array->{$clt}->email;
                    $thema = "miniERP - Oferta na produkty";
                    $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP Nowa oferta</td></tr>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 20px;text-align: center;font-family: Bahnschrift;'><br><br>".$query[0]->message."<br><br></td></tr>
            </table>
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";
                    send_mail($mail,$thema,$BodyEmail);
                }

                DB::table('offer')->where(['offer_id'=>$id])->update(array('e_date'=>date('Y-m-d H:i:s'),'e_by'=>((Auth::id())?Auth::id():'0'),'send'=>'1'));
                log_event('offer','Wysłanie oferty (id'.$id.')');
            }
        }

        return redirect('/offer');

    }
}
