<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function success( $code = 200, $message = [],$result = [], $status ="OK"){
	    return response()->json([
	    	'status' => $status,
	    	'code' => $code,
	    	'message'=> $message,
	    	'result' => $result
	    ], $code);
	}
	
	public function error($code = 400, $message = [], $result = [], $status ="error"){
	    return response()->json([
	    	'status' => $status,
	    	'code' => $code,
	    	'message'=> $message,
	    	'result' => $result
        ], $code);
	}
}
