<?php

namespace App\Controller\Api;

use App\Exception\ValidatorException;
use App\Mapper\OrderMapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;


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
        // https://github.com/nelmio/NelmioApiDocBundle/issues/1168

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
     * @SWG\Response(
     *     response=500,
     *     description="Unexpected error"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="facebookId",
     *     in="formData",
     *     type="number",
     *     description="Facebook ID"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="currency",
     *     in="formData",
     *     type="string",
     *     maxLength=3,
     *     minLength=3,
     *     description="Currency code. ISO 4217"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     name="totalCost",
     *     in="formData",
     *     type="number",
     *     description="Total cost"
     * )
     * @SWG\Parameter(
     *     required=true,
     *     default="false",
     *     name="isLegalPerson",
     *     in="formData",
     *     type="boolean",
     *     description="Is legal person?"
     * )
     * @SWG\Parameter(
     *     required=false,
     *     name="attributes",
     *     in="formData",
     *     type="string",
     *     description="Some attributes. JSON string"
     * )
     * @SWG\Tag(name="order")
     */
    public function createAction(Request $request, OrderMapper $orderMapper)
    {
        try {
            $order = $orderMapper->fromRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            return $this->json(['status' => true]);

        } catch (ValidatorException $e) {
            return $this->json([
                'status' => false,
                'errors' => $e->getErrors()
            ], 422);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => false,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
