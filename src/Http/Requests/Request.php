<?php
namespace Czim\CmsUploadModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{

    public function authorize()
    {
        return true;
    }

}
