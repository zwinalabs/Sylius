<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Translation\Provider;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleProviderInterface
{
    /**
     * @return array
     */
    public function getAvailableLocales();

    /**
     * @return string
     */
    public function getCurrentLocale();

    /**
     * @return string
     */
    public function getFallbackLocale();
}
