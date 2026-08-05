<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\AssignmentSettingsRepository;
use App\Services\AssignmentSettingsService;

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
        $strategyCode = trim((string) ($request->input('strategy_code') ?? ''));

        if ($strategyCode === '') {
            throw new ValidationException('Données invalides.', [
                'strategy_code' => 'La stratégie d’assignation est requise.',
            ]);
        }

        $settings = $this->assignmentSettingsService()->setStrategy($strategyCode);

        return Response::json([
            'status' => 'success',
            'data' => $settings->toArray(),
        ]);
    }

    private function assignmentSettingsService(): AssignmentSettingsService
    {
        return new AssignmentSettingsService(
            new AssignmentSettingsRepository(),
        );
    }
}