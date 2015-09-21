<?php

namespace Staffim\DTOBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $constraintViolations
     * @param int $code
     * @param \Exception $previous
     * @param array $attributes
     */
    public function __construct(ConstraintViolationListInterface $constraintViolations, $code = 422, \Exception $previous = null)
    {
        $this->errors = $this->groupViolations($constraintViolations);
        parent::__construct(json_encode(['errors' => $this->errors], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $violations
     * @return array
     */
    private function groupViolations(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            /* @var $violation \Symfony\Component\Validator\ConstraintViolation */
            $key = $violation->getPropertyPath() ?: '_model';
            if (!isset($errors[$key])) {
                $errors[$key] = [];
            }
            $errors[$key][] = [
                'message' => $violation->getMessage(),
                'message_template' => $violation->getMessageTemplate(),
                'attributes' => $violation->getParameters(),
            ];
        }

        return $errors;
    }
}
