<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ChatRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $path = $this->getPathInfo(); // Get the path of the current URL

          if(str_contains($path, 'get-conversation')) {
            $rules = [
                'conversation_id' => 'required|integer|exists:conversations,id',
            ];
        }
        else if(str_contains($path, 'send-message')) {
            $rules = [
                'sender_id' => 'required|integer|exists:users,id',
                'content' => 'string',
            ];
        }
        else if(str_contains($path, 'delete-conversation')) {
            $rules = [
                'conversation_id' => 'required|integer|exists:conversations,id',
            ];
        }
        else if(str_contains($path, 'delete-message')) {
            $rules = [
                'message_id' => 'required|integer|exists:messages,id',
            ];
        }
        else if(str_contains($path, 'fetch-conversationIDs')) {
            $rules = [
                'sender_id' => 'required|integer|exists:users,id',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [

            'senderId.required' => 'Sender ID is required.',
            'senderId.integer' => 'Sender ID must be an integer.',
            'senderId.exists' => 'Sender ID does not exist.',
            'receiverIds.required' => 'Receiver IDs are required.',
            'receiverIds.array' => 'Receiver IDs must be an array.',
            'receiverIds.*.integer' => 'Receiver IDs must be integers.',
            'receiverIds.*.exists' => 'One or more receiver IDs do not exist.',
            'conversation_id.required' => 'Conversation ID is required.',
            'conversation_id.integer' => 'Conversation ID must be an integer.',
            'conversation_id.exists' => 'Conversation ID does not exist.',
            'sender_id.required' => 'Sender ID is required.',
            'sender_id.integer' => 'Sender ID must be an integer.',
            'sender_id.exists' => 'Sender ID does not exist.',
            'content.required' => 'Message content is required.',
            'content.string' => 'Message content must be a string.',
            'message_id.required' => 'Message ID is required.',
            'message_id.integer' => 'Message ID must be an integer.',
            'message_id.exists' => 'Message ID does not exist.',


        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => implode(' ', $errors),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
