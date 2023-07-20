<?php

use App\Models\PaperProp;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // $props = [
    //     ['paper_type_id' => 1,    'grammage' => 190, 'size' => '35*50', 'price' => 685],
    //     ['paper_type_id' => 1,    'grammage' => 190, 'size' => '23*50', 'price' => 455],
    //     ['paper_type_id' => 1,    'grammage' => 210, 'size' => '35*50', 'price' => 750],
    //     ['paper_type_id' => 1,    'grammage' => 210, 'size' => '23*50', 'price' => 500],
    //     ['paper_type_id' => 1,    'grammage' => 230, 'size' => '35*50', 'price' => 810],
    //     ['paper_type_id' => 1,    'grammage' => 230, 'size' => '23*50', 'price' => 540],
    //     ['paper_type_id' => 1,    'grammage' => 250, 'size' => '35*50', 'price' => 875],
    //     ['paper_type_id' => 1,    'grammage' => 250, 'size' => '23*50', 'price' => 585],
    //     ['paper_type_id' => 1,    'grammage' => 270, 'size' => '35*50', 'price' => 950],
    //     ['paper_type_id' => 1,    'grammage' => 270, 'size' => '23*50', 'price' => 640],
    //     ['paper_type_id' => 1,    'grammage' => 300, 'size' => '35*50', 'price' => 1050],
    //     ['paper_type_id' => 1,    'grammage' => 300, 'size' => '23*50', 'price' => 700],
    //     ['paper_type_id' => 1,    'grammage' => 350, 'size' => '35*50', 'price' => 1265],
    //     ['paper_type_id' => 1,    'grammage' => 350, 'size' => '23*50', 'price' => 850],
    //     ['paper_type_id' => 1,    'grammage' => 400, 'size' => '35*50', 'price' => 1450],
    //     ['paper_type_id' => 1,    'grammage' => 400, 'size' => '23*50', 'price' => 1000],
    // ];

    // PaperProp::insert($props);
    return view('welcome');
});
