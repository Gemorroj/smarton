<?php

namespace App\Mapper;

use App\Entity\Order;
use App\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderMapper
{
    private $validator;
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return Order
     * @throws \TypeError|ValidatorException
     */
    public function fromRequest(Request $request)
    {
        $this->validate($request);

        return (new Order())
            ->setFacebookId($request->request->get('facebookId'))
            ->setCurrency($request->request->get('currency'))
            ->setTotalCost($request->request->get('totalCost'))
            ->setIsLegalPerson('true' === $request->request->get('isLegalPerson'))
            ->setAttributes($request->request->has('attributes') ? \json_decode($request->request->get('attributes'), true) : null);
    }


    /**
     * @param Request $request
     * @throws ValidatorException
     */
    protected function validate(Request $request)
    {
        $constraint = new Collection([
            'allowMissingFields' => true,
            'fields' => [
                'facebookId' => [
                    new NotBlank(['message' => 'facebookId является обязательным']),
                    new Length(['max' => 20, 'maxMessage' => 'Facebook ID не должен состоять не более, чем из {{ limit }} символов']),
                    new Regex(['pattern' => '/^[0-9]+$/', 'message' => 'Facebook ID должен состоять только из чисел']),
                ],
                'currency' => [
                    new NotBlank(['message' => 'currency является обязательным']),
                    new Currency(['message' => 'currency должен соответствовать ISO 4217']),
                ],
                'totalCost' => [
                    new NotBlank(['message' => 'totalCost является обязательным']),
                    new Regex(['pattern' => '/^[0-9]{1,10}((?:\.|,)[0-9]{1,2})?$/', 'message' => 'totalCost должен быть формата 12,2']),
                ],
                'isLegalPerson' => [
                    new NotNull(['message' => 'isLegalPerson является обязательным']),
                    new Choice(['message' => 'isLegalPerson должен быть true или false', 'choices' => ['true', 'false']]),
                ],
                'attributes' => [
                    new Callback(['callback' => function ($data, ExecutionContext $context) {
                        $data = @\json_decode($data, true);
                        $jsonError = \json_last_error();
                        if ($jsonError) {
                            $context->addViolation('Invalid attributes format. Need valid JSON string. Error code: ' . $jsonError);
                            return;
                        }
                        if (!\is_array($data)) {
                            $context->addViolation('Invalid attributes format. Need valid JSON string.');
                            return;
                        }
                    }])
                ],
            ]
        ]);

        $violations = $this->validator->validate(
            $request->request->all(),
            $constraint
        );
        if ($violations->count() > 0) {
            throw ValidatorException::makeException($violations);
        }
    }
}
