<?php

declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\AssignmentSettings;

/**
 * Contrat du dépôt des paramètres d'assignation.
 *
 * Il isole la logique de configuration de l'implémentation technique PDO
 * et maintient la cohérence de l'architecture Repository déjà adoptée dans
 * le projet.
 */
interface AssignmentSettingsRepositoryInterface
{
    public function find(): AssignmentSettings;

    public function updateStrategy(string $strategyCode): AssignmentSettings;

    public function updateLastAgent(?int $agentId): AssignmentSettings;
}
