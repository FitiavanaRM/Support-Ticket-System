<?php

declare(strict_types=1);

namespace App\Exceptions;

final class InvalidStateTransitionException extends StateException
{
    public function __construct(
        private readonly string $currentState,
        private readonly string $attemptedAction,
    ) {
        parent::__construct(sprintf(
            "Transition invalide : l'action '%s' n'est pas autorisee depuis l'etat '%s'.",
            $attemptedAction,
            $currentState
        ));
    }

    public function currentState(): string
    {
        return $this->currentState;
    }

    public function attemptedAction(): string
    {
        return $this->attemptedAction;
    }

    public function context(): array
    {
        return [
            'current_state' => $this->currentState,
            'attempted_action' => $this->attemptedAction,
        ];
    }
}