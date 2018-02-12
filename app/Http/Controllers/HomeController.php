<?php

namespace App\Http\Controllers;

use Chumper\Zipper\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function replacer(Request $request)
    {
        if ($request->file('zip_filer')) {
            $originalName = $request->zip_filer->getClientOriginalName();
            $path = $request->file('zip_filer')->store('files');
            $txt_files = \Zipper::make(storage_path('app/' . $path))->listFiles('/\.txt/i');
            $new_zip = new Zipper;
            $new_zip->make(storage_path('app/files/' . $originalName));
            foreach ($txt_files as $file) {
                $contents = \Zipper::make(storage_path('app/' . $path))->getFileContent($file);
                $contents = substr_replace($contents, $request->get('date_txt'), 27, 4);
                $contents = substr_replace($contents, '01000001', 34, 8);
                $new_zip->addString($file, $contents);
            }
            $new_zip->close();
        } else {
            $originalName = $request->filer->getClientOriginalName();
            $path = $request->file('filer')->store('files');
            $contents = Storage::get($path);
            $contents = substr_replace($contents, $request->get('date_txt'), 27, 4);
            $contents = substr_replace($contents, '01000001', 34, 8);
            Storage::put('files/' . $originalName, $contents);
        }
        Storage::delete($path);

        return response()->download(storage_path('app/files/' . $originalName))->deleteFileAfterSend(true);
    }
}
