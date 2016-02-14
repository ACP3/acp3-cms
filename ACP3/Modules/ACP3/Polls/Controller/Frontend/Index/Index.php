<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller\Frontend\Index
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\PollRepository
     */
    protected $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\VoteRepository
     */
    protected $voteRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Modules\ACP3\Polls\Model\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\VoteRepository $voteRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Polls\Model\PollRepository $pollRepository,
        Polls\Model\VoteRepository $voteRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
    }

    public function execute()
    {
        $polls = $this->pollRepository->getAll($this->date->getCurrentDateTime());
        $c_polls = count($polls);

        if ($c_polls > 0) {
            for ($i = 0; $i < $c_polls; ++$i) {
                if ($this->user->isAuthenticated() === true) {
                    $query = $this->voteRepository->getVotesByUserId($polls[$i]['id'], $this->user->getUserId(), $this->request->getServer()->get('REMOTE_ADDR', '')); // Check, whether the logged user has already voted
                } else {
                    $query = $this->voteRepository->getVotesByIpAddress($polls[$i]['id'], $this->request->getServer()->get('REMOTE_ADDR', '')); // For guest users check against the ip address
                }

                if ($query != 0 ||
                    $polls[$i]['start'] !== $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()
                ) {
                    $polls[$i]['link'] = 'result';
                } else {
                    $polls[$i]['link'] = 'vote';
                }
            }
            $this->view->assign('polls', $polls);
        }
    }
}
