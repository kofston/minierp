<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
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
            $row[] = '<div class="strongLabel" ><a href="/order/add/'.$row_id.'">'.$qry->order_ident.'</a></div>';
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
            'c_date'=>date('Y-m-d H:i:s'),
            'e_by'=>((Auth::id())?Auth::id():'0'),
            'order_status'=>(string)$newStatus,
        );

        switch ($newStatus){
            case -1:
                break;
            case 0:
                break;
            case 1:
                break;
            case 2:
                break;
            case 3:
                $DeliveryModel = new Delivery;
                $create_package = $DeliveryModel->add_package($orderId);
                break;
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
