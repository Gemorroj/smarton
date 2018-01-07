<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException as SymfonyValidatorException;


class ValidatorException extends SymfonyValidatorException
{
    private $errors = [];

    /**
     * @param string[]|ConstraintViolationListInterface $errors
     * @return static
     */
    public static function makeException($errors)
    {
        $obj = new static('Validation error');

        if ($errors instanceof ConstraintViolationListInterface) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $obj->addError($error->getMessage());
            }
        } else {
            $obj->setErrors($errors);
        }

        return $obj;
    }

    /**
     * @return string[]
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @param string[] $errors
     * @return $this
     */
    public function setErrors(array $errors) : self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function addError(string $error) : self
    {
        $this->errors[] = $error;
        return $this;
    }
}
