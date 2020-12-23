<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Model;
use App\Models\Currency;
use Dingo\Api\Routing\Helpers;

class ChangeLanguage
{
    use Helpers;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currencyCode = $request->header('currency', 'VND');
        $currency     = Currency::findByCodeFromCache($currencyCode);

        if (empty($currency)) {
            $this->response->error(__('middleware/api/checkcurrency.wrong_currency'), 422);
        }

        $relation           = Model::$languageToCurrency;
        $currencyToLanguage = array_flip($relation);
        $language           = array_pull($currencyToLanguage, $currencyCode, config('app.locale'));

        app()->setLocale($language);

        $user = $this->user();
        if ($user) {
            $request->headers->set('currency', $user->currency);
        }
        return $next($request);
    }
}
