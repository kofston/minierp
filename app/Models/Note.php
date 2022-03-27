<?php
namespace App\Models;

use DB;
use Request;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public $column_search = array('note.note_id','note.event','note.module','note.c_date','note.c_by');
    public $column_order = array('note.note_id','note.event','note.module','note.c_date','note.c_by');
    public $order = array('note.c_date' => 'desc');

    function _get_datatables_query()
    {
        $request = Request::instance();
        $query = DB::table('note')->where(['status'=>'1']);
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
        $query = DB::table('note')->where(['status'=>'1'])->count();
        return $query;
    }
}
