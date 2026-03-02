<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\KernelApp;

use Spryker\Client\KernelApp\KernelAppClientInterface;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\KernelApp\Dependency\Service\KernelAppToUtilEncodingServiceBridge;
use Spryker\Zed\KernelApp\Dependency\Service\KernelAppToUtilEncodingServiceInterface;

/**
 * @method \Spryker\Zed\KernelApp\KernelAppConfig getConfig()
 */
class KernelAppDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_KERNEL_APP = 'CLIENT_KERNEL_APP';

    /**
     * @var string
     */
    public const REQUEST_EXPANDER_PLUGINS = 'REQUEST_EXPANDER_PLUGINS';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'KERNEL_APP:SERVICE_UTIL_ENCODING';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addKernelAppClient($container);
        $container = $this->addRequestExpanderPlugins($container);

        return $container;
    }

    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);

        return $container;
    }

    public function addKernelAppClient(Container $container): Container
    {
        $container->set(static::CLIENT_KERNEL_APP, function (Container $container): KernelAppClientInterface {
            return $container->getLocator()->kernelApp()->client();
        });

        return $container;
    }

    protected function addRequestExpanderPlugins(Container $container): Container
    {
        $container->set(static::REQUEST_EXPANDER_PLUGINS, function () {
            return $this->getRequestExpanderPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Shared\KernelAppExtension\RequestExpanderPluginInterface>
     */
    public function getRequestExpanderPlugins(): array
    {
        return [];
    }

    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container): KernelAppToUtilEncodingServiceInterface {
            return new KernelAppToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }
}
