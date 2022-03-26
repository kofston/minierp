<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
            $insert_data['order_status'] = '0';
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
            $status_class = 'order_style_'.$qry->order_status;
            $row_id = $qry->order_id;
            $row = array("DT_RowId"=>'order_'.$row_id, 'DT_RowClass'=>'order_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/order/add/'.$row_id.'">'.$qry->order_ident.'</a></div><small><a target="_blank" href="/client/add/'.$qry->client_id.'"><u>'.$qry->name.'</u></a></small>';
            $row[] = '<ul class="m-0" style="font-size: 14px;display: block;list-style: disc;">'.$product_list.'</ul>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Rozpocznij czat" class="helpdesk_icon" href="/helpdesk/createticket/'.$row_id.'">!</a></li><li><select class="order_status_select form-control '.$status_class.'" data-orderid="'.$row_id.'"><option value="-1" '.(($qry->order_status=="-1")?'selected':'').'>Anulowane</option><option value="0" '.(($qry->order_status=="0")?'selected':'').'>Wstępne</option><option value="1" '.(($qry->order_status=="1")?'selected':'').'>Nowe</option><option value="2" '.(($qry->order_status=="2")?'selected':'').'>W realizacji</option><option value="3" '.(($qry->order_status=="3")?'selected':'').'>Zrealizowane / Wysłane</option></select></li></ul>';

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
    public function changeStatus($orderId=NULL,$newStatus=NULL)
    {
        $insert_data = array(
            'e_date'=>date('Y-m-d H:i:s'),
            'e_by'=>((Auth::id())?Auth::id():'0'),
            'order_status'=>(string)$newStatus,
        );

       $Q = DB::table('orders')->select('orders.order_id','orders.order_ident','orders.products','client.*')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status'=>'1','order_id'=>$orderId])->get();
       if(count($Q)>0)
       {
           $products = json_decode($Q[0]->products);
           $table_prod='<table cellpadding=\'2\' border=\'1\' width=\'800px;\'><tr><td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">Produkt</td><td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">Ilość</td><td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">Cena</td></tr>';

           for($i=0;$i<count($products->id);$i++)
           {
               $table_prod .='<tr>';
               $hlp_prod = 0;
               foreach ($products as $prod_K=>$prod_VAL)
               {
                   switch ($hlp_prod)
                   {
                       case 1:
                           $table_prod.='<td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">'.$products->{$prod_K}[$i].'</td>';
                           break;
                       case 2:
                           $table_prod.='<td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">'.$products->{$prod_K}[$i].'</td>';
                           break;
                       case 3:
                           $table_prod.='<td style="text-align: center;font-family: Bahnschrift;background: white;height: 45px;font-size: 17px;">'.$products->{$prod_K}[$i].' zł</td>';
                           break;
                   }
                   $hlp_prod++;
               }
               $table_prod.='</tr>';
           }
           $table_prod.='</table>';

           switch ($newStatus){
               case -1:
                   break;
               case 1:
                   $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Państwa zamówienie (".$Q[0]->order_ident.") zmieniło status na: Nowe / The new status of your order (".$Q[0]->order_ident.") is: New</td></tr>
            </table>
                ".$table_prod."
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";
                send_mail($Q[0]->email,"miniErp - Nowe zamówienie : ".$Q[0]->order_ident,$BodyEmail);
                   break;
               case 2:
                   $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Państwa zamówienie (".$Q[0]->order_ident.") zmieniło status na: W realizacji / The new status of your order (".$Q[0]->order_ident.") is: In progress</td></tr>
            </table>
               ".$table_prod."
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";
                   send_mail($Q[0]->email,"miniErp - Zamówienie przekazane do realizacji : ".$Q[0]->order_ident,$BodyEmail);
                   break;
               case 3:
                 if($Q[0]->deliver_id>0)
                 {
                     $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Państwa zamówienie (".$Q[0]->order_ident.") zmieniło status na: Wysłane / The new status of your order (".$Q[0]->order_ident.") is: Send</td></tr>
            </table>
                ".$table_prod."
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";
                     send_mail($Q[0]->email,"miniErp - Zamówienie wysłane : ".$Q[0]->order_ident,$BodyEmail);

                     $DeliveryModel = new Delivery;
                     $create_package = $DeliveryModel->add_package($orderId);
                 }
                 else
                 {
                     $BodyEmail = "<table width='800px;'>
            <tr><td style='background:#f2f2f2;box-shadow:3px 7px 5px 4px lightgray inset; text-align: center;font-size: 25px;text-align: center;font-family: Bahnschrift;'>miniERP System</td></tr>
            <tr><td style='text-align: center;font-family: Bahnschrift;background: white;height: 150px;font-size: 19px;padding-right: 40px;padding-left: 40px;'>Państwa zamówienie (".$Q[0]->order_ident.") zmieniło status na: Gotowe do odbioru / The new status of your order (".$Q[0]->order_ident.") is: Ready to pick up</td></tr>
            </table>
                ".$table_prod."
            <table width='800px;'>
            <tr><td style='height:50px;background:#323BC2;color:white;text-align: left;font-family: Bahnschrift;padding: 5px;box-shadow:-4px -3px 9px 2px whitesmoke inset;'>miniERP - ".date('Y')."</td></tr>
            </table>";
                     send_mail($Q[0]->email,"miniErp - Zamówienie gotowe do odbioru : ".$Q[0]->order_ident,$BodyEmail);
                 }
                   break;
           }
       }

        DB::table('orders')->where(['order_id'=>$orderId])->update($insert_data);
    }
    public function pack_pdf($order_id=NULL)
    {
        $PDF = $this->generate_pdf($order_id);
        if ($PDF != "" && $PDF != "error") {
            $file = "uploads/files/attachments/" . $PDF;
            if (file_exists($file)) {
                $_SESSION['offer_selected']=array();
                header("Content-type:application/pdf");
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                readfile($file);
                exit;
            } else {
                "Brak PLiku";
            }
        }
    }

}
