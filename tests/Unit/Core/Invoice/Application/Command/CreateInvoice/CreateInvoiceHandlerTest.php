<?php

namespace App\Tests\Unit\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceHandler;
use App\Core\Invoice\Domain\Exception\InvoiceException;
use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\Repository\UserRepositoryInterface;
use App\Core\User\Domain\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateInvoiceHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;

    private InvoiceRepositoryInterface|MockObject $invoiceRepository;

    private CreateInvoiceHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CreateInvoiceHandler(
            $this->invoiceRepository = $this->createMock(
                InvoiceRepositoryInterface::class
            ),
            $this->userRepository = $this->createMock(
                UserRepositoryInterface::class
            )
        );
    }

    public function testHandleSuccess(): void
    {
        $user = $this->createMock(User::class);
        // One time in this test and one time in CreateInvoiceHandler
        $user->expects($this->exactly(2))
            ->method('hasActiveStatus')
            ->willReturn(true);

        $invoice = new Invoice(
            $user, 12500
        );

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willReturn($user);

        $this->invoiceRepository->expects(self::once())
            ->method('save')
            ->with($invoice);

        $this->invoiceRepository->expects(self::once())
            ->method('flush');

        $this->handler->__invoke(new CreateInvoiceCommand('test@test.pl', 12500));
    }

    public function testHandleUserNotExists(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willThrowException(new UserNotFoundException());

        $this->handler->__invoke(new CreateInvoiceCommand('test@test.pl', 12500));
    }

    public function testHandleInvoiceInvalidAmount(): void
    {
        $this->expectException(InvoiceException::class);

        $this->handler->__invoke(new CreateInvoiceCommand('test@test.pl', -5));
    }

    public function testHandleInvoiceInactiveUserStatus(): void
    {
        $this->expectException(InvoiceException::class);
        $this->expectExceptionMessage('Użytkownik jest nie aktywny, tylko aktywni użytkownicy mogą tworzyć nowe faktury.');

        $user = $this->createMock(User::class);
        $user->expects(self::once())
            ->method('hasActiveStatus')
            ->willReturn(false);

        $this->userRepository->expects(self::once())
            ->method('getByEmail')
            ->willReturn($user);

        $this->handler->__invoke(new CreateInvoiceCommand('test@test.pl', 1));
    }
}
