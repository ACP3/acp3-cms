<?php

namespace ACP3\Modules\Articles\Controller;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Module controller of the articles frontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     *
     * @var Articles\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Articles\Model($this->db);
    }

    public function actionIndex()
    {
        $time = $this->date->getCurrentDateTime();

        $articles = $this->model->getAll($time, POS, $this->auth->entries);
        $c_articles = count($articles);

        if ($c_articles > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->model->countAll($time)
            );
            $pagination->display();

            for ($i = 0; $i < $c_articles; ++$i) {
                $articles[$i]['date_formatted'] = $this->date->format($articles[$i]['start']);
                $articles[$i]['date_iso'] = $this->date->format($articles[$i]['start'], 'c');
            }

            $this->view->assign('articles', $articles);
        }
    }

    public function actionDetails()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id, $this->date->getCurrentDateTime()) === true) {
            $cache = new Articles\Cache($this->model);
            $article = $cache->getCache($this->uri->id);

            $this->breadcrumb->replaceAnchestor($article['title'], 0, true);

            $this->view->assign('page', Core\Functions::splitTextIntoPages(Core\Functions::rewriteInternalUri($article['text']), $this->uri->getUriWithoutPages()));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}