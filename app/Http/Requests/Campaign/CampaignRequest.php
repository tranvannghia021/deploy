<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CampaignRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required',
            'subject'=>'required|min:6',
            'thumb'=>'required',
            'email_content'=>'required',
            'email_footer'=>'required',
            'customize_email'=>'required',
        ];
    }


    public function messages()
    {
        return [
            'name.required'=>'Name campaign is required!',
            'subject.required'=>'Subject campaign is required!',
            'subject.min'=>'Subject at Least 6 Characters!',
            'thumb.required'=>'Thumb campaign banner is required!',
            'email_content.required'=>'Email content campaign is required!',
            'email_footer.required'=>'Email footer campaign is required!',
            'customize_email.required'=>'Customize email campaign is required!'
        ];
    }


    protected function failedValidation(Validator $validator) 
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'message' => $errors,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

}
