<?php
namespace App\Models;

use DB;
use Illuminate\Support\Facades\Auth;
use Request;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public $column_search = array('delivery.delivery_id','delivery.order_id','delivery.c_date','delivery.status');
    public $column_order = array('delivery.delivery_id','delivery.order_id','delivery.c_date','delivery.status');
    public $order = array('delivery.c_date' => 'desc');

//    DHL
    function add_package($order_id=NULL)
    {
        if(isset($order_id))
        {
            $DHL_OBJECT = new \SoapClient('https://sandbox.dhl24.com.pl/webapi2',array());
            $request = Request::instance();
            $query = DB::table('orders')->select('orders.order_id','orders.order_ident','client.*')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status'=>'1','order_id'=>$order_id])->get();
            $check_exist_delivery = DB::table('delivery')->where(['status'=>'1','order_id'=>$order_id])->get();
            if(count($check_exist_delivery)==0)
            {
                foreach ($query as $qry)
                {
                    $requestData = array(
                        'authData'=> array('username'=>'LUKKONOP_TEST','password'=>'cF3VpQ0ZmIoB6o*'),
                        'shipments'=>array(
                            //paczuszka
                            'item'=>array(
                                'shipper'=>array(
                                    'name'=>'miniERP Łukasz Konop',
                                    'postalCode'=>'38200',
                                    'city'=>'JASŁO',
                                    'street'=>'NOWAKOWA',
                                    'houseNumber'=>'123',
                                    'contactPhone'=>''
                                ),
                                'receiver'=>array(
                                    //B Company / C Customer
                                    'addressType'=>'B',
                                    'country'=>strtoupper($qry->country), //PL
                                    'name'=>substr($qry->name, 0, 59),
                                    'postalCode'=>str_replace("-","",$qry->zip), //np 38200
                                    'city'=>$qry->city,
                                    'street'=>$qry->street,
                                    'houseNumber'=>$qry->building,
                                    'apartmentNumber' =>(($qry->alt!="")?$qry->alt:''),

                                ),
                                'pieceList'=>array(
                                    'item'=>array(
                                        'type'=>'PACKAGE', //PACKAGE
                                        'width'=>'50',
                                        'height'=>'50',
                                        'length'=>'50',
                                        'weight'=>'15',
                                        'quantity'=>'1',
                                        'nonStandard'=>false,
                                    )
                                ),
                                'payment'=>array(
                                    'paymentMethod'=>'BANK_TRANSFER',
                                    'payerType'=>'SHIPPER',
                                    'accountNumber'=>'6000000', //6000000 sandbox
                                ),
                                'skipRestrictionCheck'=>true, //To znaczy ze maja umowe chyba i ze zamawiaja paczke bez kuriera
                                'service'=>array(
                                    'product'=>((strtoupper($qry->country)=='PL')?'AH':'PI'),
                                ),
                                'shipmentDate'=> date('Y-m-d',strtotime("+ 1 day")),
                                'content'=>"Przesyłka testowa",
                                'comment'=>"Przesyłka testowa praca magisterska Ł.Konop",
                            )
                        )
                    );

                    try {
                        $response = $DHL_OBJECT->__soapCall('createShipments',array($requestData));
                    }
                    catch (Exception $e)
                    {
//                   $this->system_m->update('transport_parcel',array('error'=>(string)$e),$id_from_db);
                    }

                    if(isset($response->createShipmentsResult->item->shipmentId))
                    {
                        DB::table('delivery')->insert(array('order_id'=>$qry->order_id,'number'=>$response->createShipmentsResult->item->shipmentId,'c_date'=>date('Y-m-d H:i:s'),'c_by'=>((Auth::id())?Auth::id():'0'),'status'=>'1'));
//                        return redirect('/delivery');
                    }
                    else
                    {

                    }

                }
            }
        }
        else
        {
//            return redirect('/delivery');
        }
    }
    function send_package()
    {

    }
//

    function _get_datatables_query()
    {
        $request = Request::instance();
        $query = DB::table('delivery')->select('delivery.*','orders.order_ident')->leftJoin('orders', 'delivery.order_id', '=', 'orders.order_id')->where(['delivery.status'=>'1']);
//        ## SEARCH DATATABLE
        $search = $request->post('search');
        $i = 0;
        if(isset($search['value']))
        {
            $sql_search = '';
            foreach ($this->column_search as $item) // loop column
            {
                if($search['value']) // if datatable send POST for search
                {
                    if($i===0)
                        $query->where($item,'like','%'.$search['value'].'%');
                    else
                        $query->orWhere($item,'like','%'.$search['value'].'%');
                }
                $i++;
            }
        }
        ## ORDER DATATABLE
        $order_post = $request->post('order');

        if(is_array($order_post) )
        {
            $query->orderBy($this->column_order[$order_post['0']['column']], $order_post['0']['dir']);
        }else if(isset($this->order))
        {
            $order = $this->order;
            $query->orderBy(key($order), $order[key($order)]);
        }


        return $query;
    }
    function datatable_rows()
    {
        $query = $this->_get_datatables_query();
        return $query->get();
    }
    function count_filtered()
    {
        $query = $this->_get_datatables_query();
        $query = $query->count();
        return $query;
    }

    public function count_all()
    {
        $query = DB::table('delivery')->where(['status'=>'1'])->count();
        return $query;
    }
}
