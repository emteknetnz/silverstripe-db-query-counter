<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;

class DBQueryCounterMiddleware implements HTTPMiddleware
{
    public function process(HTTPRequest $request, callable $delegate)
    {
        $session = $request->getSession();
        $keys = ['log'];
        foreach ($keys as $key) {
            if ($session->get($key) === null) {
                $session->set($key, false);
            }
            $val = $request->getVar($key);
            if ($val !== null) {
                $session->set($key, $val == 1);
            }
        }
        return $delegate($request);
    }
}
