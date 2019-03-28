<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentController extends Controller
{
    private function saveFile(UploadedFile $file = null) {
        $type = ['pdf' => 1, 'doc' => 2, 'docx' => 3, 'txt' => 4, 'md' => 5];

        $name = $file->getFilename();
        $size = $file->getSize();
        if ($size > 2048000) { //2M
            return msg(3, '文件大小' . __LINE__);
        }

        $allow_ext = array_keys($type);
        $extension = $file->getClientOriginalExtension();

        if (in_array($extension, $allow_ext)) {
            $savePath = storage_path().'/document';
            $filename = time().rand(0, 1000);
            $file->move($savePath, $filename);

            $type = $type[$extension];
            $data = array(
                'size' => $size,
                'name' => $name,
                'filename' => $filename,
                'type' => $type,
                'uploader' => session('id')
            );

            return $data;
        } else {
            return false;
        }
    }

    public function upload(Request $request) {
        $mod = array(
            'title' => '/^[\s\S]{0,300}$/',
            'description' => '/^[\s\S]{0,600}$/',
            'score' => '/^(1|)\d&/',
            'tag' => '/^\d$/'
        );

        if (!$request->hasFile('document') || !$request->has(array_keys($mod))) {
            return msg(1, __LINE__);
        }

        $data = $request->only(array_keys($mod));
        if (!check($mod, $data)) {
            return msg(3, '数据格式错误' . __LINE__);
        }

        $fileinfo = $this->saveFile($request->file('document'));
        $data = array_merge($data, $fileinfo);

        $document = new Document($data);
        $result = $document->save();

        if($result) {
            return msg(0, null);
        } else {
            unlink(storage_path().'/document/'.$data['filename']);
            return msg(4, __LINE__);
        }
    }
}
