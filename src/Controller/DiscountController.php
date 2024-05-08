<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\CostCalculator;
use App\DTO\CostDetailsDto;

class DiscountController extends AbstractController
{

    #[Route('/api/calculate', name: 'calculateTotalCost', methods: ['POST'])]
    public function calculateTotalCost(
        Request $request,
        ValidatorInterface $validator,
        CostCalculator $costCalculator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $costDetails = new CostDetailsDto();
        $costDetails->baseCost = $data['baseCost'];
        $costDetails->participantBirthdate = $data['participantBirthdate'];
        $costDetails->startDate = $data['startDate'];
        $costDetails->paymentDate = $data['paymentDate'];

        $errors = $validator->validate($costDetails);

        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorsArray], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Данные прошли валидацию, передаем объект costDetails в сервис
        $cost = $costCalculator->calculate($costDetails);

        // Возвращаем итоговую стоимость в ответе
        return $this->json(['итоговая стоимость' => $cost]);
    }

}