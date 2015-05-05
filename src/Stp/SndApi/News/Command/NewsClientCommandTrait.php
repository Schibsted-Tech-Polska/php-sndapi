<?php

namespace Stp\SndApi\News\Command;

use Stp\SndApi\News\Client;
use Symfony\Component\Console\Input\InputInterface;

trait NewsClientCommandTrait
{
    private $newsClient;

    private function initNewsClient(InputInterface $input)
    {
        $secret = $input->getOption('secret');
        $publicationId = $input->getOption('publicationId');

        $this->newsClient = new Client($secret, $publicationId);
    }

    /**
     * @return Client
     */
    public function getNewsClient(InputInterface $input)
    {
        if (empty($this->newsClient)) {
            $this->initNewsClient($input);
        }

        return $this->newsClient;
    }

    /**
     * @param Client $newsClient
     */
    public function setNewsClient(Client $newsClient)
    {
        $this->newsClient = $newsClient;
    }
}
