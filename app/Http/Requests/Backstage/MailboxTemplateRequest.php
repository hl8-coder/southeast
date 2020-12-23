<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Language;
use App\Models\MailboxTemplate;
use Illuminate\Validation\Rule;

class MailboxTemplateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currencies  = Currency::getAll()->pluck('code')->toArray();
        $languages   = Language::getAll()->pluck('code')->toArray();
        $isAffiliate = $this->get('is_affiliate');
        $methodName  = $this->getRequestMethod();
        switch ($methodName) {
            case 'store':
                return [
                    'type'                 => [
                        'required', 'in:' . get_validate_in_string(MailboxTemplate::$types),
                        Rule::unique('mailbox_templates')->where(function ($query) use ($isAffiliate) {
                            return $query->where('is_affiliate', $isAffiliate);
                        }),
                    ],
                    'currencies'           => 'required|array',
                    'currencies.*'         => 'required|in:' . implode(',', $currencies),
                    'languages'            => 'required|array',
                    'languages.*.language' => 'required|in:' . implode(',', $languages),
                    'languages.*.title'    => 'required|string|max:255',
                    'languages.*.body'     => 'required|string',
                ];
                break;
            case 'update':
                return [
                    'currencies'           => 'nullable|array',
                    'currencies.*'         => 'required|in:' . implode(',', $currencies),
                    'languages'            => 'nullable|array',
                    'languages.*.language' => 'nullable|in:' . implode(',', $languages),
                    'languages.*.title'    => 'nullable|string|max:255',
                    'languages.*.body'     => 'nullable|string',
                ];
                break;
            default:
                return [
                    'currencies'           => 'nullable|array',
                    'currencies.*'         => 'required|in:' . implode(',', $currencies),
                    'type'                 => 'required|in:' . get_validate_in_string(MailboxTemplate::$types),
                    'languages'            => 'required|array',
                    'languages.*.language' => 'required|in:' . implode(',', $languages),
                    'languages.*.title'    => 'required|string|max:255',
                    'languages.*.body'     => 'required|string',
                ];
                break;
        }
    }
}
