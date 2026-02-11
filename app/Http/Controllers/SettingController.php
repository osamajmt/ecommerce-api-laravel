<?php

namespace App\Http\Controllers;

use App\Models\setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //
     public function index(){
        $settings=setting::all();
        return response()->json(['settings' => $settings,
            'status'=>"success",],200);
    }
}
