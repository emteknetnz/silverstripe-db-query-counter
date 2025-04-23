<?php

namespace emteknetnz\DBQueryCounter;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\View\Requirements;

class DBQueryMiddleware implements HTTPMiddleware
{
    use Configurable;

    private static $redirect_seconds = 3;

    public function process(HTTPRequest $request, callable $delegate)
    {
        $this->exec($request);
        return $delegate($request);
    }

    private function exec(HTTPRequest $request)
    {
        $session = $request->getSession();
        if ($session->get('log') === null) {
            $session->set('log', false);
        }
        $val = $request->getVar('log');
        if ($val !== null) {
            $session->set('log', $val == 1);
            if (!$request->isAjax()) {
                if ($val == 1) {
                    // ?log=1
                    $milliseconds = DBQueryMiddleware::config()->get('redirect_seconds') * 1000;
                    Requirements::customScript(implode(PHP_EOL, [
                        'setTimeout(function() {',
                        '  document.location.href = document.location.href.replace("?log=1", "?log=0");',
                        "}, $milliseconds);",
                    ]));
                } else {
                    // ?log=0
                    (new DBQueryReportWriter)->run();
                    DBQueryLogger::reset();
                }
            }
        }
    }
}
