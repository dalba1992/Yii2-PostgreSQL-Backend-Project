<?php
namespace app\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use yii\data\Pagination;
use yii\widgets\LinkPager;

class ALinkPager extends LinkPager
{
    /**
     * @inherit
     */
    public $options = ['class' => 'pagination'];

    public $prevPageLabel = 'Previous';
    public $nextPageLabel = 'Next';
    public $firstPageLabel = 'First';
    public $lastPageLabel = 'Last';

    public $prevPageCssClass = 'previous fg-button ui-button ui-state-default';
    public $nextPageCssClass = 'next ui-corner-tl ui-corner-bl fg-button ui-button';
    public $firstPageCssClass = 'first ui-corner-tl ui-corner-bl fg-button ui-button ui-state-default';
    public $lastPageCssClass = 'last ui-corner-tr ui-corner-br fg-button ui-button';

    public $disabledPageCssClass = 'ui-state-disabled';
    /**
     * @var string a wrapper tag for the pagination
     */
    public $wrapperTag = null;
    /**
     * @var string a wrapper class for the pagination
     */
    public $wrapperClass = null;
    /**
     * @var string a wrapper style for the pagination
     */
    public $wrapperStyle = null;

    /**
     * Initializes the pager.
     */
    public function init()
    {
        if ($this->pagination === null) {
            throw new InvalidConfigException('The "pagination" property must be set.');
        }
    }

    /**
     * Renders the page buttons.
     * @return string the rendering result
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->pagination->getPage();

        // first page
        if ($this->firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($this->firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }

        // internal pages
        $pageButtons = [];
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $pageButtons[] = $this->renderPageButton($i + 1, $i, 'fg-button ui-button ui-state-default', false, $i == $currentPage);
        }
        if(!empty($pageButtons))
            $buttons[] = '<span>'.implode("", $pageButtons).'</span>';

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        // last page
        if ($this->lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($this->lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        $html = Html::tag('div', implode("", $buttons), $this->options);

        if($this->wrapperTag){
            return Html::tag($this->wrapperTag, $html, ['class'=>$this->wrapperClass, 'style'=>$this->wrapperStyle]);
        }
        else
            return $html;
    }

    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button.
     * @param boolean $disabled whether this page button is disabled
     * @param boolean $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => $class === '' ? null : $class];

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
            return Html::a($label, $this->pagination->createUrl($page), $options);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            return Html::tag('a', $label, $options);
        }

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        $linkOptions['class'] = $class;
        return Html::a($label, $this->pagination->createUrl($page), $linkOptions);
    }
}
