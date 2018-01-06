<?php

namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class OrderController extends Controller
{
    /**
     * @Route("/api/order/list", methods={"GET"}, requirements={"_format": "json"}, name="api_order_list")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all orders",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Tag(name="order")
     */
    public function listAction()
    {
        // @SWG\Schema(type="array", @Model(type=Order::class))
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $orders = $repository->findAll();
        return $this->json([
            'status' => true,
            'data' => [
                'orders' => $orders,
            ],
        ]);
    }


    /**
     * @Route("/api/order/create", methods={"POST"}, requirements={"_format": "json"}, name="api_order_create")
     * @SWG\Response(
     *     response=200,
     *     description="Success"
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Validation error"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="facebookId",
     *     in="query",
     *     type="number",
     *     description="Facebook ID"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="currency",
     *     in="query",
     *     type="string",
     *     description="Currency code"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="totalCost",
     *     in="query",
     *     type="number",
     *     description="Total cost"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="isLegalPerson",
     *     in="query",
     *     type="boolean",
     *     description="Is legal person?"
     * )
     * @SWG\Parameter(
     *     required=false,
     *     name="attributes",
     *     in="query",
     *     type="string",
     *     description="Some attributes. JSON string"
     * )
     * @SWG\Tag(name="order")
     */
    public function createAction(Request $request, ValidatorInterface $validator)
    {
        $attributes = null;
        if ($request->get('attributes')) {
            try {
                $attributes = $this->makeJsonArray($request->get('attributes'));
            } catch (\InvalidArgumentException $e) {
                return $this->json([
                    'status' => false,
                    'errors' => [$e->getMessage()]
                ], 422);
            }
        }

        $order = (new Order())
            ->setFacebookId($request->get('facebookId'))
            ->setCurrency($request->get('currency'))
            ->setTotalCost($request->get('totalCost'))
            ->setIsLegalPerson('true' === $request->get('isLegalPerson'))
            ->setAttributes($attributes);

        $errors = $validator->validate($order);
        if ($errors->count() > 0) {
            $arrayErrors = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $arrayErrors[] = $error->getMessage();
            }
            return $this->json([
                'status' => false,
                'errors' => $arrayErrors
            ], 422);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        return $this->json(['status' => true]);
    }

    /**
     * @param string $attributes
     * @return array
     */
    private function makeJsonArray(string $attributes)
    {
        $data = @\json_decode($attributes, true);
        $jsonError = \json_last_error();
        if ($jsonError) {
            throw new \InvalidArgumentException('Invalid attributes format. Need valid JSON string. Error code: ' . $jsonError);
        }
        if (!\is_array($data)) {
            throw new \InvalidArgumentException('Invalid attributes format. Need valid JSON string. ');
        }

        return $data;
    }
}
