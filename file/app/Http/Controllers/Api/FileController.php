<?php

namespace App\Http\Controllers\Api;

use App\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FileController extends Controller
{
    public function upload(Request $request) {
        $file = [];

        if($request->file)
        {
            $fileName = time().'.'.$request->file->getClientOriginalExtension();
            $file['name'] = $request->file->getClientOriginalName();
            $request->file->move(public_path('file'), $fileName);
            $file['path'] = 'http://localhost:8084/file/'.$fileName;
            $file['conversation_id'] = $request->conversation_id;

            $file['type'] = ($request->type == 'image') ? true : null;
            $newFile = File::create($file);

            return response($newFile);
        }
    }
    
    public function fileInfo($id) {
        $file = File::where('id', $id)->get();
        return response($file);
    }

    public function getFileByConversation(Request $request) {
        $files = File::where('conversation_id', $request->conversation_id)
        ->orderBy('created_at', 'desc')->get();
        return response($files);
    }
    
    public function deleteFile($fileId) {
        $file = File::find($fileId);
        $file->delete();
        return response($file);
    }
}
