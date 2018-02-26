<?php

namespace App\Http\Controllers;

use App\Classes\Replacer;
use Chumper\Zipper\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function replacer(Request $request)
    {
        if ($request->file('zip_filer')) {
            $path_download = $this->replaceZipFile($request);
        } else {
            $path_download = $this->replaceSimpleFile($request);
        }

        return response()->download($path_download)->deleteFileAfterSend(true);
    }

    public function replaceSimpleFile(Request $request)
    {
        $originalName = $request->filer->getClientOriginalName();
        $path = $request->file('filer')->storeAs('files', $originalName);
        $replacer = new Replacer($path, true);
        $replacer->changeDate($request->get('date_txt'))
            ->changeCodeProvedor('01000001')
            ->sum_products()
            ->save('files_changed/');
        Storage::delete($path);

        return $replacer->getPathSaved();
    }

    public function replaceZipFile(Request $request)
    {
        $originalName = $request->zip_filer->getClientOriginalName();
        $path = $request->file('zip_filer')->store('files');
        $txt_files = \Zipper::make(storage_path('app/' . $path))->listFiles('/\.txt/i');
        $new_zip = new Zipper;
        $new_zip->make(storage_path('app/files/' . $originalName));
        foreach ($txt_files as $file) {
            $replacer = new Replacer();
            $replacer->setContent(\Zipper::make(storage_path('app/' . $path))->getFileContent($file))
                ->changeDate($request->get('date_txt'))
                ->changeCodeProvedor('01000001')
                ->sum_products();
            $new_zip->addString($file, $replacer->getContent());
        }
        $new_zip->close();
        Storage::delete($path);

        return storage_path('app/files/' . $originalName);
    }
}
