<?php
namespace Czim\CmsUploadModule\Http\Requests;

class UploadFileRequest extends Request
{

    public function rules()
    {
        return [
            'file'      => 'required|file',
            'name'      => 'string',
            'reference' => 'string',
        ];
    }

}
