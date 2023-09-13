<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponsiveScreen extends Controller
{
  


    public function index(){
        // $imageUrl = '/home/rathod/Desktop/canidesk/storage/images/ticket.jpeg';
        return view('sla.responsivescreen');
    }
}
