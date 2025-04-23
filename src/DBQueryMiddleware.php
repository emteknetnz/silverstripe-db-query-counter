<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\View\Requirements;

class DBQueryMiddleware implements HTTPMiddleware
{
    public function process(HTTPRequest $request, callable $delegate)
    {
        $session = $request->getSession();
        if ($session->get('log') === null) {
            $session->set('log', false);
        }
        $val = $request->getVar('log');
        if ($val !== null) {
            $session->set('log', $val == 1);
            if ($val == 1) {
                // ?log=1
                Requirements::customScript(implode(PHP_EOL, [
                    'setTimeout(function() {',
                    '  document.location.href = document.location.href.replace("?log=1", "?log=0");',
                    '}, 3000);',
                ]));
            } else {
                // ?log=0
                (new DBQueryReportWriter(false))->run();
                DBQueryLogger::reset();
            }
        }
        return $delegate($request);
    }
}
