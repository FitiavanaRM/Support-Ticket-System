<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\AssignmentSettingsRepository;
use App\Services\AssignmentSettingsService;
use App\Support\View;

// Contrôleur HTTP pour la configuration de l'assignation des tickets.
// Il relie la couche web aux services métier et garde la logique de validation
// dans le service et les exceptions métier déjà présents dans le projet.
final class AssignmentSettingsController
{
    public function index(Request $request): Response
    {
        $settings = $this->assignmentSettingsService()->current();

        return Response::json([
            'status' => 'success',
            'data' => $settings->toArray(),
        ]);
    }

    public function update(Request $request): Response
    {
        try {
            $strategyCode = trim((string) ($request->input('strategy_code') ?? ''));

            if ($strategyCode === '') {
                throw new ValidationException('Données invalides.', [
                    'strategy_code' => 'La stratégie d’assignation est requise.',
                ]);
            }

            $settings = $this->assignmentSettingsService()->setStrategy($strategyCode);

            if ($request->acceptsHtml()) {
                return Response::redirect('/assignment-settings');
            }

            return Response::json([
                'status' => 'success',
                'data' => $settings->toArray(),
            ]);
        } catch (ValidationException $exception) {
            if ($request->acceptsHtml()) {
                return Response::html(View::render(__DIR__ . '/../Views/settings/index.php', [
                    'errors' => array_values($exception->context()),
                    'old' => ['strategy_code' => $request->input('strategy_code')],
                ]));
            }

            return Response::json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                ...$exception->context(),
            ], $exception->httpStatusCode());
        }
    }

    private function assignmentSettingsService(): AssignmentSettingsService
    {
        return new AssignmentSettingsService(
            new AssignmentSettingsRepository(),
        );
    }
}