<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Authentication;

use Aura\Session\Segment;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Aura\Web\WebFactory;
use Cw\LearnBear\AppSpi\LoggerInterface;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;

class CwSession implements SessionHandlerInterface
{
    private readonly Session $session;
    private readonly Segment $segment;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->session = (new SessionFactory())->newInstance(
            (new WebFactory($GLOBALS))->newRequest()->cookies->getArrayCopy()
        );
        $this->segment = $this->session->getSegment(self::SESS_SEGMENT);
    }

    public function setAuth(string $uuid): void
    {
        $this->segment->set('userIdentity', $uuid);
    }

    public function isNotAuthorized(): bool
    {
        $this->logger->log('userIdentity: ' . $this->segment->get('userIdentity', 'no valid session.'));

        return empty($this->segment->get('userIdentity'));
    }

    public function clearAuth(): void
    {
        $this->segment->clear();
    }

    public function setFlashMessage(string $message, string $key): void
    {
        $this->segment->setFlash($key, $message);
    }

    public function getFlashMessage(string $key): ?string
    {
        $message = $this->segment->getFlash($key);
        $this->segment->clearFlash();

        return $message;
    }

    public function destroy(): void
    {
        if (! $this->session->isStarted()) {
            return;
        }

        $this->session->destroy();
    }
}
