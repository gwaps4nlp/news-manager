<?php 

namespace Gwaps4nlp\NewsManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsCreateRequest extends FormRequest {
	
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
			'content' => 'required|max:10000',
			'send_by_email' => 'required|boolean',
			'language_id' => 'required|exists:languages,id',
			'hour_scheduled' => 'required_if:send_by_email,1|regex:/[0-9]{2}:[0-9]{2}/',
			'date_scheduled' => 'required_if:send_by_email,1|date_format:d/m/Y',
			'title' => 'required_if:send_by_email,1|max:75',
		];
	}

}