<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Request;

class ProductController extends Controller
{
    private $module;
    private $baseview;
    public function __construct()
    {
        $this->module='product';
        $this->baseview='product';
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
        $ProductModel = new Product;
        $query = $ProductModel->get_details($id);
        return view($this->module."/".$this->baseview,['query'=>json_encode($query)]);
    }
    public function save($id=NULL)
    {
        $insert_data = array(
            'name'=>$_POST['name'],
            'symbol'=>$_POST['symbol'],
            'price'=>$_POST['price'],
            'unit'=>$_POST['unit'],
            'c_date'=>date('Y-m-d H:i:s'),
            'e_by'=>((Auth::id())?Auth::id():'0'),
            'status'=>'1',
        );

        if(file_exists('cache/products.php'))
            unlink('cache/products.php');

        if(isset($id) && $id!='')
        {
            $insert_data['e_date'] = date('Y-m-d H:i:s');
            DB::table('product')->where(['product_id'=>$id])->update($insert_data);
            return redirect('/product?msg=success_updated');
        }
        else
        {
            $count_all = DB::table('product')->count();
            DB::table('product')->insert($insert_data);
            return redirect('/product?msg=success_insert');
        }

    }
    public function delete($id=NULL)
    {
        if(isset($id))
            DB::table('product')->where(['product_id'=>$id])->update(['status'=>'0','e_date'=>date('Y-m-d H:i:s'),'e_by'=>((Auth::id())?Auth::id():'0')]);

        if(file_exists('cache/products.php'))
            unlink('cache/products.php');
    }
    public function get_list($page = 1)
    {
        $request = Request::instance();
        $data = [];
        $j=1;

        $ProductModel = new Product;
        $query = $ProductModel->datatable_rows();


        foreach($query as $qry)
        {
            $row_id = $qry->product_id;
            $row = array("DT_RowId"=>'product_'.$row_id, 'DT_RowClass'=>'product_tr');
            $row[] = $j++;
            $row[] = '<div class="strongLabel" ><a href="/product/add/'.$row_id.'">'.$qry->name.'</a></div><small>'.$qry->symbol.'</small>';
            $row[] = '<span>'.$qry->price.' zł ('.$qry->unit.')</span>';
            $row[] = $qry->c_date;
            $row[] = (($qry->e_date!='0000-00-00 00:00:00')?$qry->e_date:'-');
            $row[] = '<ul><li><a title="Edytuj" href="/product/add/'.$row_id.'">&#9998;</a></li><li><a class="redtext" title="Usuń" href="/product/delete/'.$row_id.'">&#10006;</a></li></ul>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $request->post('draw'),
            "recordsTotal" => $ProductModel->count_all(),
            "recordsFiltered" => $ProductModel->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);

    }
}
