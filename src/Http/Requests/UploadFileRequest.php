<?php
namespace Czim\CmsUploadModule\Http\Requests;

class UploadFileRequest extends Request
{

    public function rules()
    {
        return [
            'file'       => 'required|file',
            'name'       => 'string',
            'reference'  => 'string',
            'validation' => 'nullable|json',
        ];
    }

    /**
     * Returns validation rules as string or array.
     * Anything else will return null.
     *
     * @return string|array|null
     */
    public function getNormalizedValidationRules()
    {
        $rules = json_decode($this->get('validation'), true);

        if ( ! is_string($rules) && ! is_array($rules)) {
            return null;
        }

        return $rules;
    }

}
