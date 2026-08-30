<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Processor;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\Validator\Validator\ValidatorInterface
};

use Kit\Utils\Shared\Sanitizer\DataSanitizer;

use App\Presentation\Shared\Responder\HttpResponder;

final class RequestProcessor
{
    /**
     * @template T of object
     *
     * @param T $request
     * @param ValidatorInterface|null $validator
     *
     * @return JsonResponse|null
    */
    public static function process(object $request, ?ValidatorInterface $validator = null): ?JsonResponse
    {
        if (!method_exists($request, 'errors')) {
            return null;
        }

        $errorsRaw = $request->errors($validator);
        if (!is_array($errorsRaw)) {
            return null;
        }

        $errors = self::sanitizeErrors($errorsRaw);

        if (!empty($errors)) {
            return HttpResponder::unprocessableEntity($errors);
        }

        return null;
    }

    /**
     * @param array<mixed> $errorsRaw
     *
     * @return array<string, string>
    */
    private static function sanitizeErrors(array $errorsRaw): array
    {
        $errors = [];

        foreach ($errorsRaw as $key => $value) {
            $errors[(string) $key] = DataSanitizer::sanitizeString($value);
        }

        return $errors;
    }
}
