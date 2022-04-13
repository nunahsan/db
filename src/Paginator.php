<?php

namespace Nunahsan\Db;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Paginator extends \Illuminate\Support\ServiceProvider {

    protected static $page = 1;
    protected static $rows_per_page = 10;
    protected static $select = null;

    public function boot() {
        
    }

    public function register() {
        
    }

    public static function get(Builder $select, $PaginationRows = 10) {
        self::$select = $select;
        self::$page = (int) request()->get('page', 1);
        self::$rows_per_page = $PaginationRows;

        //set limit and offset
        $select->limit(self::$rows_per_page);
        $select->offset = (self::$rows_per_page * self::$page) - self::$rows_per_page;

        //result
        return (Object) [
                    'items' => $select->get()->all(),
                    'pagination' => self::set_pagination()
        ];
    }

    protected static function set_pagination() {
        //reset limit, offset, order
        unset(self::$select->limit, self::$select->offset, self::$select->orders);

        if (!empty(self::$select->groups)) {
            $sql = "select count(1) as total from (" . self::$select->toSql() . ") as dummy";
            $total_rows = DB::select($sql, self::$select->getBindings())[0]->total;
        } else {
            self::$select->columns = [DB::raw('count(1) as total')];
            $total_rows = self::$select->first()->total;
        }

        $last_page = ceil($total_rows / self::$rows_per_page);

        return (Object) [
                    'current_page' => self::$page,
                    'total_rows' => $total_rows,
                    'first_page' => $total_rows > 0 ? 1 : NULL,
                    'last_page' => $last_page,
                    'prev_page' => self::$page == 1 ? NULL : self::$page - 1,
                    'next_page' => self::$page == $last_page ? NULL : self::$page + 1
        ];
    }

}
