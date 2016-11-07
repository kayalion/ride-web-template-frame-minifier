<?php

namespace ride\web\template\huqis\func;

use huqis\func\TemplateFunction;
use huqis\TemplateContext;

use ride\service\MinifierService;

class ScriptTemplateFunction implements TemplateFunction {

    public function __construct(MinifierService $minifierService) {
        $this->minifierService = $minifierService;
    }

    public function call(TemplateContext $context, array $arguments) {
        $result = null;
        $scripts = $context->getVariable('app.minifier.scripts', array());

        if ($arguments) {
            // add script
            $scripts[$arguments[0]] = true;
        } elseif ($scripts) {
            // render scripts
            $tags = array();

            $minifiedScripts = $this->minifierService->minifyJs(array_keys($scripts));
            foreach ($minifiedScripts as $minifiedScript) {
                if (strpos($minifiedScript, '<script') === 0) {
                    $tags[] = $minifiedScript;
                } else {
                    $tags[] = '<script type="text/javascript" src="' . $minifiedScript . '"></script>';
                }
            }

            $scripts = array();
            $result = implode("\n        ", $tags);
        }

        $context->setVariable('app.minifier.scripts', $scripts);

        return $result;
    }

}
