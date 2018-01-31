<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function replacer(Request $request)
    {
        $originalName = $request->filer->getClientOriginalName();
        $path = $request->file('filer')->store('files');
        $contents = Storage::get($path);
        $contents = substr_replace($contents, $request->get('replace_txt'), 34, strlen($request->get('replace_txt')));
        Storage::put('files/' . $originalName, $contents);
        Storage::delete($path);
        return response()->download(storage_path('app/files/' . $originalName))->deleteFileAfterSend(true);
    }
}
