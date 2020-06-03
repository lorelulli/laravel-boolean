<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request){
        $data = $request->all();
        $path = Storage::disk('public')->put('images', $data['path']);
        dd($path);
    }
    public function update(Request $request, $id){

        $photo = [
            'id'=> 1,
            'title'=> 'foto 1',
            'description' => 'Lorem ipsum dolor sit amet',
            'path'=> '/images/pBAT6eHdeZrP9tyoU35nEac4p03TE73vpRMLOxOD.jpeg'

        ];
        $data = $request->all();
        if (isset($data['path'])) {
            Storage::disk('public')->delete($photo['path']);
        }

    }
}
