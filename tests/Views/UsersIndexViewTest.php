<?php

declare(strict_types=1);

namespace Tests\Views;

use App\Models\User;
use App\Support\View;
use PHPUnit\Framework\TestCase;

final class UsersIndexViewTest extends TestCase
{
    public function testUsersIndexViewRendersUserData(): void
    {
        $user = new User(
            id: 8,
            name: 'Agent Test',
            email: 'agent@test.com',
            passwordHash: 'hash',
            role: 'AGENT',
            isActive: true,
        );

        $html = View::render(__DIR__ . '/../../src/Views/users/index.php', [
            'users' => [$user],
            'currentUser' => null,
        ]);

        $this->assertStringContainsString('Agent Test', $html);
        $this->assertStringContainsString('agent@test.com', $html);
    }
}
