<?php


namespace Staffim\DTOBundle\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class DTOFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('is_dto', function ($arg) {
                return sprintf('!is_scalar(%s)', $arg);
            }, function (array $variables, $value) {
                return !is_scalar($value);
            }),
        ];
    }
}
q
