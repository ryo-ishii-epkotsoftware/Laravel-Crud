<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|max255',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '名称',
        ];
    }

    protected function getRedirectUrl()
    {
        if (request()->routeIs('*.update')) {
            $url = $this->redirector->getUrlGenerator();
            return $url->route('admin.jobs.edit', ['job' => request()->route()->parameter('job')]);
        }
        return parent::getRedirectUrl();
    }
}
