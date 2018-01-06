<?php

namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $orders = $repository->findAll();
        return $this->json([
            'status' => true,
            'data' => [
                'orders' => $orders,
            ],
        ]);
    }
}
