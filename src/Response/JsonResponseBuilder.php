<?php

namespace App\Response;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class JsonResponseBuilder
{
    private $twig;
    private $kernel;

    public function __construct(Environment $twig, KernelInterface $kernel)
    {
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    /**
     * @param $result
     * @param $template
     * @param $templateVars
     * @param int $code
     * @return JsonResponse
     */
    public function buildWithTemplate(
        $result,
        $template,
        $templateVars,
        $code = 200
    ): JsonResponse {
        try {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'result' => $result,
                    'html' => $this->twig->render(
                        $template,
                        $templateVars
                    ),
                ],
                $code
            );
        } catch (\Exception $e) {
            return $this->buildErrorResponse();
        }
    }

    /**
     * @param $result
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    public function buildWithData(
        $result,
        array $data,
        $code = 200
    ): JsonResponse {
        try {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'result' => $result,
                    'data' => $data,
                ],
                $code
            );
        } catch (\Exception $e) {
            return $this->buildErrorResponse();
        }
    }

    private function buildErrorResponse(\Exception $exception): JsonResponse
    {
        if ($this->kernel->isDebug()) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'result' => 'exception',
                    'html' => $exception->getMessage(),
                ],
                500
            );
        }
        try {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'result' => 'exception',
                    'html' => $this->twig->render('common/_json_error.twig'),
                ],
                500
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'result' => 'exception',
                    'html' => 'Error',
                ],
                500
            );
        }
    }
}
