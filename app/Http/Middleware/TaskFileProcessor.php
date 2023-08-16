<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Services\ImageIntervention;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class TaskFileProcessor
{
    const IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'gif', 'tiff'];

    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $file = $request->task_file;
        $path = '';
        $filename = Helper::fileName() .'.';
        if ($request->hasFile('task_file')) {
            $ext = explode('/', $file->getMimeType())[1];
            if(in_array(mb_strtolower($ext), self::IMAGE_TYPES)){
                $filename = $filename.'jpg';
                $path = Storage::path($file->storeAs('/public/task_files', $filename));
                ImageIntervention::imageIntervention($path);
            }
            else{
                if (($file->getSize() / 1024 / 1024) > 100) {
                    $error[] = [
                        'error'      => 'File too large',
                        'message'    => 'The maximum filesize is 100MB.',
                        'error_type' => 'File is too large.',
                    ];
                    return response()->json($error);
                }

                $filename = $filename.$file->extension();
                $file->storeAs('task_file', $filename);
                }
            $request->merge(['file_for_task' => $filename]);
        }

        return $next($request);
    }

}
