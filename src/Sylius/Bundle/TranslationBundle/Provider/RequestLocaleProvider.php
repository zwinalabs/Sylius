<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\Provider;

use Sylius\Component\Translation\Provider\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RequestLocaleProvider implements LocaleProviderInterface, EventSubscriberInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            // IMPORTANT to keep priority 34.
            KernelEvents::REQUEST => [['onKernelRequest', 34]],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->request = $event->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        return ['en_US', 'es_ES', 'pl_PL', 'de_DE', 'fr_FR'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        if (null === $this->request) {
            return $this->getFallbackLocale();
        }

        return $this->request->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocale()
    {
        return $this->defaultLocale;
    }
}
