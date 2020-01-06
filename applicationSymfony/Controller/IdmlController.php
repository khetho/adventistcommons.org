<?php

namespace App\Controller;

use AdventistCommons\Idml\DomManipulation\Exception as IdmlException;
use App\Product\Idml\Validator;
use App\Product\Form\Type\ValidateIdmlType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IdmlController extends AbstractController
{
    /**
     * @Route("/validate", name="app_product_validate_idml")
     * @param Request $request
     * @param Validator $validator
     * @return Response
     */
    public function validate(Request $request, Validator $validator)
    {
        $idmlValidationForm = $this->createForm(ValidateIdmlType::class);
        $idmlValidationForm->handleRequest($request);
        if ($idmlValidationForm->isSubmitted() && $idmlValidationForm->isValid()) {
            /** @var File $file */
            $file = $idmlValidationForm->getData()['idmlFile'];
            $newPathname = $file->getFilename().'.idml';
            $file->move($file->getPath(), $newPathname);
            try {
                $validator->validate($file->getPathname().'.idml');
                $this->addFlash('success', 'messages.idml.successful');
            } catch (IdmlException $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('app_product_validate_idml');
        }

        return $this->render('product/idmlValidation/validate_idml.html.twig', [
            'idmlValidationForm' => $idmlValidationForm->createView(),
        ]);
    }
}
