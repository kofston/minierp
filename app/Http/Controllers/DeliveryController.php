<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('hooks');
    }
    public function index()
    {
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
    public function delete($id=NULL,$deliveryNumb=NULL)
    {
        if(isset($id))
            DB::table('delivery')->where(['delivery_id'=>$id])->update(['status'=>'0','e_date'=>date('Y-m-d H:i:s'),'e_by'=>((Auth::id())?Auth::id():'0')]);

        $DHL_OBJECT = new \SoapClient('https://sandbox.dhl24.com.pl/webapi2',array());
        $requestData = array(
            'authData'=> array('username'=>'LUKKONOP_TEST','password'=>'cF3VpQ0ZmIoB6o*'),
            'shipments'=>array(
                'item'=>array($deliveryNumb),
            )
        );
        try {
            log_event('delivery','Usunięcie przesyłki ('.$deliveryNumb.')');
            $response = $DHL_OBJECT->__soapCall('deleteShipments', array($requestData));
        }
        catch (Exception $e)
        {

        }
        return redirect('/delivery');

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
            $row[] = '<div class="strongLabel" >'.$qry->order_ident.'</div>';
            $row[] = (($qry->c_date!='0000-00-00 00:00:00')?$qry->c_date:'-');
            $row[] = '<ul><li><a title="Pobierz etykietę" class="greentext" href="/delivery/get_label/'.$qry->number.'">&#10066;</a></li><li><a class="redtext" title="Usuń" href="/delivery/delete/'.$row_id.'">&#10006;</a></li></ul>';

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

    public function get_label($delivery_hash=NULL)
    {
        if(isset($delivery_hash))
        {
            $DHL_OBJECT = new \SoapClient('https://sandbox.dhl24.com.pl/webapi2',array());
            $requestData = array(
                'authData'=> array('username'=>'LUKKONOP_TEST','password'=>'cF3VpQ0ZmIoB6o*'),
                'itemsToPrint'=>array(
                    'item'=>array(
                        'labelType'=>'BLP',
                        'shipmentId'=>$delivery_hash,
                    )
                )
            );
            $response = $DHL_OBJECT->__soapCall('getLabels',array($requestData));
            if(isset($response->getLabelsResult->item->labelData))
            {
                file_put_contents('cache/etykieta.pdf', base64_decode($response->getLabelsResult->item->labelData));
                $file_to_save = 'cache/etykieta.pdf';
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="etykieta.pdf"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($file_to_save));
                header('Accept-Ranges: bytes');
                readfile($file_to_save);
            }
            else
            {
                echo 'Wystąpił błąd, brak etykietki do pobrania z API...za chwilę zostaniesz przekierowany...';
                sleep(4);
                return redirect('/parcel');
            }
        }
    }
}
