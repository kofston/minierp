<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Offer;
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
    }
    public function index()
    {
        checkAccess();
        return view($this->module."/".$this->baseview);
    }
    public function get_list($page = 1)
    {
        $request = Request::instance();
        $data = [];
        $j=1;

        $OfferModel = new Offer;
        $query = $OfferModel->datatable_rows();


        foreach($query as $qry)
        {
            $row_id = $qry->offer_id;
            $row = array("DT_RowId"=>'offer_'.$row_id, 'DT_RowClass'=>'offer_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/offer/add/'.$row_id.'">'.$qry->name.'</a></div>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/offer/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/offer/delete/'.$row_id.'">&#10006;</a></li></ul>';

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
}
