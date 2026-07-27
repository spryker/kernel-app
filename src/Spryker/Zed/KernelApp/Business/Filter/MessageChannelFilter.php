<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\KernelApp\Business\Filter;

use Spryker\Zed\KernelApp\KernelAppConfig;
use Spryker\Zed\KernelApp\Persistence\KernelAppRepositoryInterface;

class MessageChannelFilter implements MessageChannelFilterInterface
{
    /**
     * @var \Spryker\Zed\KernelApp\Persistence\KernelAppRepositoryInterface
     */
    protected KernelAppRepositoryInterface $kernelAppRepository;

    /**
     * @var \Spryker\Zed\KernelApp\KernelAppConfig
     */
    protected KernelAppConfig $kernelAppConfig;

    public function __construct(
        KernelAppRepositoryInterface $kernelAppRepository,
        KernelAppConfig $kernelAppConfig
    ) {
        $this->kernelAppRepository = $kernelAppRepository;
        $this->kernelAppConfig = $kernelAppConfig;
    }

    /**
     * @param array<string> $messageChannelNames
     *
     * @return array<string>
     */
    public function filterMessageChannels(array $messageChannelNames): array
    {
        $appConfigTransfers = $this->kernelAppRepository->getActiveAppConfigs(
            $this->kernelAppConfig->getAppConfigGracePeriod(),
        );

        $filteredMessageChannelNames = [];

        foreach ($appConfigTransfers as $appConfigTransfer) {
            if (!$appConfigTransfer->getMessageChannels()) {
                // At this point the feature is not enabled fully on the Tenant's side,
                // because the message channels are required for EVERY application to work properly.
                // So we can only return the original message channel names.
                return $messageChannelNames;
            }

            $filteredMessageChannelNames = array_merge(
                $filteredMessageChannelNames,
                array_intersect($messageChannelNames, $appConfigTransfer->getMessageChannels()),
            );
        }

        return array_values(array_unique($filteredMessageChannelNames));
    }
}
