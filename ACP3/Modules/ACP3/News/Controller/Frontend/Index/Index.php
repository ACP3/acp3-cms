<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\News\Controller\Frontend\Index
 */
class Index extends AbstractAction
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\News\Model\NewsRepository
     */
    protected $newsRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext          $context
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Helpers\StringFormatter                     $stringFormatter
     * @param \ACP3\Core\Pagination                                  $pagination
     * @param \ACP3\Modules\ACP3\News\Model\NewsRepository           $newsRepository
     * @param \ACP3\Modules\ACP3\Categories\Helpers                  $categoriesHelpers
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Pagination $pagination,
        News\Model\NewsRepository $newsRepository,
        Categories\Helpers $categoriesHelpers,
        Categories\Model\CategoryRepository $categoryRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->stringFormatter = $stringFormatter;
        $this->newsRepository = $newsRepository;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int $cat
     *
     * @return array
     */
    public function execute($cat = 0)
    {
        // Kategorie in Brotkrümelspur anzeigen
        if ($cat !== 0 && $this->newsSettings['category_in_breadcrumb'] == 1) {
            $this->seo->setCanonicalUri($this->router->route('news'));
            $this->breadcrumb->append($this->translator->t('news', 'news'), 'news');
            $category = $this->categoryRepository->getTitleById($cat);
            if (!empty($category)) {
                $this->breadcrumb->append($category);
            }
        }

        $time = $this->date->getCurrentDateTime();
        $news = $this->fetchNews($cat, $time);
        $cNews = count($news);

        if ($cNews > 0) {
            $this->pagination->setTotalResults($this->newsRepository->countAll($time, $cat));

            for ($i = 0; $i < $cNews; ++$i) {
                $news[$i]['text'] = $this->view->fetchStringAsTemplate($news[$i]['text']);
                if ($this->commentsActive === true && $news[$i]['comments'] == 1) {
                    $news[$i]['comments_count'] = $this->get('comments.helpers')->commentsCount(
                        'news',
                        $news[$i]['id']
                    );
                }
                if ($this->newsSettings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
                    $news[$i]['text'] = $this->stringFormatter->shortenEntry(
                        $news[$i]['text'],
                        $this->newsSettings['readmore_chars'], 50,
                        '...<a href="' . $this->router->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->translator->t('news',
                            'readmore') . "]</a>\n"
                    );
                }
            }
        }

        return [
            'news' => $news,
            'dateformat' => $this->newsSettings['dateformat'],
            'categories' => $this->categoriesHelpers->categoriesList('news', $cat),
            'pagination' => $this->pagination->render()
        ];
    }

    /**
     * @param int    $cat
     * @param string $time
     *
     * @return array
     */
    private function fetchNews($cat, $time)
    {
        if (!empty($cat)) {
            $news = $this->newsRepository->getAllByCategoryId($cat, $time, POS, $this->user->getEntriesPerPage());
        } else {
            $news = $this->newsRepository->getAll($time, POS, $this->user->getEntriesPerPage());
        }

        return $news;
    }
}
