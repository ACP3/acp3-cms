<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Send extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    protected $newsletterHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository
     */
    protected $accountRepository;

    /**
     * Send constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                               $context
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository        $newsletterRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository $accountRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter                         $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\Model\Repository\NewslettersRepository $newsletterRepository,
        Newsletter\Model\Repository\NewsletterAccountsRepository $accountRepository,
        Newsletter\Helper\SendNewsletter $newsletterHelpers
    ) {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
        $this->accountRepository = $accountRepository;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute(int $id)
    {
        if ($this->newsletterRepository->newsletterExists($id) === true) {
            $accounts = $this->accountRepository->getAllActiveAccounts();
            $cAccounts = \count($accounts);
            $recipients = [];

            for ($i = 0; $i < $cAccounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $bool = $this->newsletterHelpers->sendNewsletter($id, $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterRepository->update(['status' => '1'], $id);
            }

            return $this->redirectMessages()->setMessage(
                $bool === true && $bool2 !== false,
                $this->translator->t(
                    'newsletter',
                    $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'
                )
            );
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
