<?php

namespace samuelelonghin\gridview;

use kartik\base\Config;
use samuelelonghin\btn\Btn;
use samuelelonghin\grid\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\grid\Column;
use yii\helpers\Html;
use yii\helpers\Url;


/**
 * Class GridView
 * @package samuelelonghin\gridview
 *
 * @property Column[] $mergeColumns
 */
class GridView extends \kartik\grid\GridView
{

    public $isAssociative = false;
    public $itemClass = false;
    /**
     * @var bool|Column[]
     */
    public $mergeColumns = false;
    public $query;
    public $rowClickUrl = false;
    public $rowClick = true;
    public $rowClickParams = null;
    public $pk = 'id';
    public $baseColumns = false;
    public $preGrid = '';
    public $postGrid = '';
    public $title = false;
    public $containerClass = 'rounded shadow mt-5 mb-5 p-3';
    public $visible = true;
    public $hover = true;
    public $striped = false;
    public $bordered = false;
    public $summary = '';
    public $showOnEmpty = false;
    public $responsive = true;
    public $responsiveWrap = false;
    public $emptyText = '';
    public $level = 0;
    public $cornerButton;
    public $cornerIcon;
    public $cornerButtonUrl;
    public $collapse = true;
    public $limit = null;

    private $isEmpty = false;

    public $moduleId = 'gridview-s';
    
    public function init()
    {
        if (!$this->visible) return;
        if (!isset($this->dataProvider)) {
            if (isset($this->query)) {
                $pagination = [];
                if (!is_null($this->limit)) {
                    if ($this->limit)
                        $pagination['pageSize'] = $this->limit;
                }
                $this->dataProvider = new ActiveDataProvider(['query' => $this->query, 'pagination' => $pagination]);
            } else {
                throw new InvalidConfigException('Il campo "query" deve essere impostato');
            }
        }
        if (!$this->dataProvider->count) {
            $this->isEmpty = true;
        }
        if (!$this->itemClass) {
            if (isset($this->dataProvider->query))
                $this->itemClass = $this->dataProvider->query->modelClass;
            else throw new InvalidConfigException('Manca itemClass');
        }
        if (!$this->isEmpty && !$this->columns) {
            if (!$this->baseColumns) {
                $this->columns = $this->itemClass::getGridViewColumns();
            } else {
                $this->columns = array_merge($this->baseColumns, $this->columns);
            }
            if ($this->mergeColumns) {
                $this->columns = array_merge($this->columns, $this->mergeColumns);
            }
        }
        if ($this->emptyText) {
            $this->emptyText = '<p class="text-muted">' . Yii::t('app', $this->emptyText) . '</p>';
        }
        if ($this->summary) {
            $this->summary = '<h5>' . Yii::t('app', $this->summary) . '</h5>';
        }
        if ($this->rowClick && !$this->rowOptions) {
            if (!$this->rowClickUrl) {
                $this->rowClickUrl = '/' . $this->itemClass::getController() . '/view';
            }
            $urlClick = $this->rowClickUrl;
            $pk = $this->pk;
            $params = $this->rowClickParams;
            if (!$params) $params = [];
            $this->rowOptions = function ($model) use ($urlClick, $pk, $params) {
                $params[$pk] = $model[$pk];
                $params[] = $urlClick;
                $url = Url::toRoute($params, 'https');
                //                $url = Url::toRoute([$urlClick, $pk => $model[$pk]], 'https');
                return [$pk => $model[$pk], 'onclick' => 'cambiaPagina(event,"' . $url . '");'];
            };
        }
        if ($this->cornerButton) {
            $this->cornerButton = Btn::widget(['type' => 'expand', 'url' => $this->cornerButtonUrl ?: false, 'icon' => $this->cornerIcon ?: 'expand', 'text' => false]);
        }
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->visible && (!$this->isEmpty || $this->emptyText)) {
            if ($this->containerClass) {
                $this->initContainer();
                $this->renderTitle();
                $this->renderPreGrid();
                parent::run();
                $this->renderPostGrid();
                $this->endContainer();
            } else {
                $this->renderPreGrid();
                $this->renderTitle();
                parent::run();
                $this->renderPostGrid();
            }
        }
    }

    public function initContainer()
    {
?>
        <div class="<?= $this->containerClass ?>">
        <?php
    }

    public function endContainer()
    {
        ?>
        </div>
        <?php
    }

    public function renderPreGrid()
    {
        echo $this->preGrid;
    }

    public function renderPostGrid()
    {
        echo $this->postGrid;
    }

    private function renderTitle()
    {
        if (is_string($this->title)) {

            $headingNumber = 2 + $this->level;
        ?>
            <div class="row">
                <div class="col">
                    <h<?= $headingNumber ?>><?= Html::encode($this->title) ?></h<?= $headingNumber ?>>
                </div>
                <div class="px-3 ml-auto">
                    <?= $this->cornerButton ?>
                </div>
            </div>
<?php
        }
    }

    public function getRowClickUrlComposed()
    {
        if ($this->rowClickUrl)
            return $this->rowClickUrl;
        if ($this->rowClickParams) {
        }
    }

    protected function initModule()
    {
        if (!isset($this->moduleId)) {
            $this->_module = Module::getInstance();
            if (isset($this->_module)) {
                $this->moduleId = $this->_module->id;
                return;
            }
            $this->moduleId = Module::MODULE;
        }
        $this->_module = Config::getModule($this->moduleId, Module::class);
        if (isset($this->bsVersion)) {
            return;
        }
    }
}
