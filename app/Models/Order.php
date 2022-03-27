<?php

namespace App\Models;

use DB;
use Request;
use Illuminate\Database\Eloquent\Model;
use Dompdf\Dompdf;

class Order extends Model
{
    public $column_search = array('orders.order_id', 'orders.order_ident', 'orders.c_date', 'orders.e_date', 'orders.order_status', 'client.name');
    public $column_order = array('orders.order_id', 'orders.order_ident', 'orders.c_date', 'orders.e_date');
    public $order = array('orders.c_date' => 'desc');

    function get_details($id)
    {
        if (!is_null($id))
            $query = DB::table('orders')->select('orders.*', 'client.client_id', 'client.rabate')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status' => '1', 'order_id' => $id])->get();
        else
            $query = array();

        return $query;
    }

    function _get_datatables_query()
    {
        $request = Request::instance();
        $query = DB::table('orders')->select('orders.*', 'client.client_id', 'client.name')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status' => '1']);
//        ## SEARCH DATATABLE
        $search = $request->post('search');
        $i = 0;
        if (isset($search['value'])) {
            $sql_search = '';
            foreach ($this->column_search as $item) // loop column
            {
                if ($search['value']) // if datatable send POST for search
                {
                    if ($i === 0)
                        $query->where($item, 'like', '%' . $search['value'] . '%');
                    else
                        $query->orWhere($item, 'like', '%' . $search['value'] . '%');
                }
                $i++;
            }
        }
        ## ORDER DATATABLE
        $order_post = $request->post('order');

        if (is_array($order_post)) {
            $query->orderBy($this->column_order[$order_post['0']['column']], $order_post['0']['dir']);
        } else if (isset($this->order)) {
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
        $query = DB::table('orders')->where(['status' => '1'])->count();
        return $query;
    }

    function generate_pdf($id = NULL)
    {
        if (isset($id)) {
            $query = DB::table('orders')->select('orders.order_id', 'orders.order_ident', 'orders.products', 'client.*')->leftJoin('client', 'orders.client_id', '=', 'client.client_id')->where(['orders.status' => '1', 'order_id' => $id])->get();

            $product_list = '';
            $product_list_q = json_decode($query[0]->products);

            $SUM = 0;
            for ($i = 0; $i < count((array)$product_list_q->name); $i++) {
                $product_list .= '<p style="font-size:14px;margin:0;padding:3px;border:solid 1px #ddd;font-weight:bold;"><span style="display:block;font-size:13px;font-weight:normal;">' . $product_list_q->name[$i] . '</span> ilość: ' . $product_list_q->qty[$i] . ' <b style="font-size:12px;font-weight:300;"> / ' . $product_list_q->price[$i] . ' zl</b></p>';
                $SUM = $SUM + (float)number_format($product_list_q->qty[$i] * $product_list_q->price[$i], 2, '.', '');
            }
            $TEMPLATE = '
 <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <table style="width: 100%;margin:50px auto 10px;background-color:#fff;padding:50px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);-moz-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); border-top: solid 10px purple;">
    <thead>
      <tr>
        <th style="text-align:left;width: 100%;max-width: 100%;">Faktura</th>
        <th style="text-align:right;font-weight:400;width: 100%;max-width: 100%;">Wystawiono: ' . date('d-m-Y') . '</th>
      </tr>
    </thead>
    <tbody>
      <tr style="width: 100%;max-width: 100%;">
        <td style="height:35px;width: 100%;max-width: 100%;"></td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td colspan="2" style="border: solid 1px #ddd; padding:10px 20px;width: 100%;max-width: 100%;">
          <p style="font-size:14px;margin:0 0 0 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Zamówienie: </span> ' . $query[0]->order_ident . '</p>
        </td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td style="height:10px;width: 100%;max-width: 100%;"></td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td style="width:50%;padding:20px;vertical-align:top;max-width: 100%;">
          <p style="margin:0 0 4px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px">Nazwa</span> ' . $query[0]->name . '</p>
          <p style="margin:0 0 4px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Email</span> ' . $query[0]->email . '</p>
          <p style="margin:0 0 4px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Tel</span> ' . $query[0]->phone . '</p>
        </td>
        <td style="width:50%;padding:20px;vertical-align:top;max-width: 100%">
          <p style="margin:0 0 4px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Adres</span> ' . $query[0]->street . ' ' . $query[0]->building . ' ' . (($query[0]->alt != '') ? ' / ' . $query[0]->alt : '') . '<br>' . $query[0]->zip . ' ' . $query[0]->city . '<br>' . $query[0]->country . '</p>
        </td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td colspan="2" style="font-size:20px;padding:30px 15px 0 15px;width: 100%;max-width: 100%;">Produkty</td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td colspan="2" style="padding:15px;width: 100%;max-width: 100%;">
        ' . $product_list . '
        </td>
      </tr>
      <tr style="width: 100%;max-width: 100%;">
        <td colspan="2" style="padding: 5px;width: 100%;max-width: 100%;">RAZEM: ' . $SUM . ' zl/td>
      </tr>
    </tbody>
  </table>
  </html>';

            $dompdf = new Dompdf();
            $dompdf->loadHtml($TEMPLATE);

            $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
            $dompdf->render();

// Output the generated PDF to Browser
            file_put_contents('uploads/invoices/Invoice.pdf', $dompdf->output());

        } else
            return 0;
    }
}
