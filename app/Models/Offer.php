<?php
namespace App\Models;

use DB;
use Request;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    public $column_search = array('offer.offer_id','offer.message','offer.clients','offer.c_date','offer.offer_id');
    public $column_order = array('offer.offer_id','offer.message','offer.clients','offer.c_date','offer.offer_id');
    public $order = array('offer.c_date' => 'desc');

    function get_details($id)
    {
        if(!is_null($id))
            $query = DB::table('offer')->where(['offer.status'=>'1','offer_id'=>$id])->get();
        else
            $query = array();

        return $query;
    }
    function _get_datatables_query()
    {
        $request = Request::instance();
        $query = DB::table('offer')->where(['status'=>'1']);
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
        $query = DB::table('offer')->where(['status'=>'1'])->count();
        return $query;
    }
}
