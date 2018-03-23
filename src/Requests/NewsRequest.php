<?php 

namespace Gwaps4nlp\NewsManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest {
	
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
			'id' => 'required|exists:news'
		];
	}

}