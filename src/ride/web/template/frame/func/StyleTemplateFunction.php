<?php

namespace ride\web\template\frame\func;

use frame\library\func\TemplateFunction;
use frame\library\TemplateContext;

use ride\service\MinifierService;

class StyleTemplateFunction implements TemplateFunction {

    public function __construct(MinifierService $minifierService) {
        $this->minifierService = $minifierService;
    }

    public function call(TemplateContext $context, array $arguments) {
        $result = null;
        $styles = $context->getVariable('app.minifier.styles', array());

        if ($arguments) {
            // add style
            $src = $arguments[0];
            if (isset($arguments[1])) {
                $media = $arguments[1];
            } else {
                $media = 'screen';
            }

            if (!isset($styles[$media])) {
                $styles[$media] = array();
            }
            $styles[$media][$src] = true;
        } elseif ($styles) {
            // render styles
            $tags = array();

            foreach ($styles as $media => $mediaStyles) {
                $minifiedStyles = $this->minifierService->minifyCss(array_keys($mediaStyles));
                foreach ($minifiedStyles as $minifiedStyle) {
                    $tags[] = '<link rel="stylesheet" type="text/css" href="' . $minifiedStyle . '" media="' . $media . '">';
                }
            }

            $styles = array();
            $result = implode("\n        ", $tags);
        }

        $context->setVariable('app.minifier.styles', $styles);

        return $result;
    }

}
