<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors  = (new ValidationException($validator))->errors();

        $data = [];
        foreach ($errors as $key => $value) {
            $data[] = $value[0];
        }
        $response = [
            'status' => JsonResponse::HTTP_BAD_REQUEST,
            'body'  => $data
        ];

        throw new HttpResponseException(
            response()->json(
               $response
            , JsonResponse::HTTP_OK)
        );
        
    }

}
