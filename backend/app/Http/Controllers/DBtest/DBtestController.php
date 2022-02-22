<?php

namespace App\Http\Controllers\DBtest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class DBtestController extends Controller
{
    /** 
     * @return View
    */
    public function DBtest()
    {
        try {
            DB::connection('sqlsrv')->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
        return json_encode(DB::connection('sqlsrv')->select("select @@version"));
    }
}
