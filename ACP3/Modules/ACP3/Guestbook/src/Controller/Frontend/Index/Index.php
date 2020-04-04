<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Emoticons\Helpers;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository
     */
    protected $guestbookRepository;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers|null
     */
    protected $emoticonsHelpers;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                     $context
     * @param \ACP3\Core\Pagination                                             $pagination
     * @param \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository $guestbookRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Guestbook\Model\Repository\GuestbookRepository $guestbookRepository,
        ?Helpers $emoticonsHelpers = null
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->guestbookRepository = $guestbookRepository;
        $this->emoticonsHelpers = $emoticonsHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Guestbook\Installer\Schema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->guestbookRepository->countAll($this->guestbookSettings['notify']));

        $guestbook = $this->guestbookRepository->getAll(
            $this->guestbookSettings['notify'],
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        foreach ($guestbook as $i => $row) {
            if ($this->emoticonsHelpers && $this->guestbookSettings['emoticons'] == 1) {
                $guestbook[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($row['message']);
            }
        }

        try {
            return [
                'guestbook' => $guestbook,
                'overlay' => $this->guestbookSettings['overlay'],
                'pagination' => $this->pagination->render(),
                'dateformat' => $this->guestbookSettings['dateformat'],
            ];
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}