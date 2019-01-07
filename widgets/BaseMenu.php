<?php

namespace zikwall\encore\modules\core\widgets;

use Yii;
use yii\helpers\Url;

/**
 * BaseMenu is the base class for navigations.
 */
class BaseMenu extends \yii\base\Widget
{
    const EVENT_INIT = 'init';
    const EVENT_RUN = 'run';

    /**
     *
     * @var array
     */
    public $items = [];

    /**
     *
     * @var array
     */
    public $itemGroups = [];

    /**
     *
     * @var string тип навигации, необязательный для идентификации.
     */
    public $type = "";

    /**
     * @var string идентификатор элемента dom
     */
    public $id;

    /**
     * Шаблон навигации
     *
     * Доступные шаблоны по умолчанию:
     * - leftNavigation
     * - tabMenu
     *
     * @var string
     */
    public $template;


    public function init()
    {
        $this->addItemGroup([
            'id' => '',
            'label' => ''
        ]);

        // Yii 2.0.11 представил собственное событие init
        if (version_compare(Yii::getVersion(), '2.0.11', '<')) {
            $this->trigger(self::EVENT_INIT);
        }

        return parent::init();
    }

    public function addItem($item)
    {
        if (!isset($item['label'])) {
            $item['label'] = 'Unnamed';
        }

        if (!isset($item['url'])) {
            $item['url'] = '#';
        }

        if (!isset($item['icon'])) {
            $item['icon'] = '';
        }


        if (!isset($item['community'])) {
            $item['community'] = '';
        }

        if (!isset($item['htmlOptions'])) {
            $item['htmlOptions'] = [];
        }

        if (!isset($item['pjax'])) {
            $item['pjax'] = true;
        }

        if (isset($item['target'])) {
            $item['htmlOptions']['target'] = $item['target'];
        }

        if (!isset($item['sortOrder'])) {
            $item['sortOrder'] = 1000;
        }

        if (!isset($item['newItemCount'])) {
            $item['newItemCount'] = 0;
        }

        if (!isset($item['isActive'])) {
            $item['isActive'] = false;
        }
        if (isset($item['isVisible']) && !$item['isVisible']) {
            return;
        }

        if (!isset($item['htmlOptions']['class'])) {
            $item['htmlOptions']['class'] = "";
        }

        if ($item['isActive']) {
            $item['htmlOptions']['class'] .= "active";
        }

        if (isset($item['id'])) {
            $item['htmlOptions']['class'] .= " " . $item['id'];
        }

        $this->items[] = $item;
    }

    public function addItemGroup($itemGroup)
    {
        if (!isset($itemGroup['id']))
            $itemGroup['id'] = 'default';

        if (!isset($itemGroup['label']))
            $itemGroup['label'] = 'Unnamed';

        if (!isset($itemGroup['icon']))
            $itemGroup['icon'] = '';

        if (!isset($itemGroup['sortOrder']))
            $itemGroup['sortOrder'] = 1000;

        if (isset($itemGroup['isVisible']) && !$itemGroup['isVisible'])
            return;

        $this->itemGroups[] = $itemGroup;
    }

    public function getItems($group = "")
    {
        $this->sortItems();

        $ret = [];

        foreach ($this->items as $item) {

            if ($group == $item['group'])
                $ret[] = $item;
        }

        return $ret;
    }

    private function sortItems()
    {
        usort($this->items, function ($a, $b) {
            if ($a['sortOrder'] == $b['sortOrder']) {
                return 0;
            } else
            if ($a['sortOrder'] < $b['sortOrder']) {
                return - 1;
            } else {
                return 1;
            }
        });
    }

    private function sortItemGroups()
    {
        usort($this->itemGroups, function ($a, $b) {
            if ($a['sortOrder'] == $b['sortOrder']) {
                return 0;
            } else
            if ($a['sortOrder'] < $b['sortOrder']) {
                return - 1;
            } else {
                return 1;
            }
        });
    }

    public function getItemGroups()
    {
        $this->sortItemGroups();
        return $this->itemGroups;
    }

    public function run()
    {
        $this->trigger(self::EVENT_RUN);

        if (empty($this->template)) {
            return;
        }

        return $this->render($this->template, array());
    }

    public function setActive($url)
    {
        foreach ($this->items as $key => $item) {
            if ($item['url'] == $url) {
                $this->items[$key]['htmlOptions']['class'] = 'active';
                $this->items[$key]['isActive'] = true;
                $this->view->registerJs('encore.modules.ui.navigation.setActive("' . $this->id . '", ' . json_encode($this->items[$key]) . ');', \yii\web\View::POS_END, 'active-' . $this->id);
            }
        }
    }

    public function getActive()
    {
        foreach ($this->items as $item) {
            if ($item['isActive']) {
                return $item;
            }
        }
    }

    public function setInactive($url)
    {
        foreach ($this->items as $key => $item) {
            if ($item['url'] == $url) {
                $this->items[$key]['htmlOptions']['class'] = '';
                $this->items[$key]['isActive'] = false;
            }
        }
    }

    /**
     * Добавьте активный класс из пункта меню.
     */
    public static function markAsActive($url)
    {
        if (is_array($url)) {
            $url = Url::to($url);
        }

        \yii\base\Event::on(static::className(), static::EVENT_RUN, function($event) use($url) {
            $event->sender->setActive($url);
        });
    }

    /**
     * Эта функция используется в сочетании с pjax, чтобы убедиться, что необходимое меню активно
     */
    public static function setViewState()
    {
        $instance = new static();
        if (!empty($instance->id)) {
            $active = $instance->getActive();
            $instance->view->registerJs('encore.modules.ui.navigation.setActive("' . $instance->id . '", ' . json_encode($active) . ');', \yii\web\View::POS_END, 'active-' . $instance->id);
        }
    }

    /**
     * Удаляет активный класс из пункта меню.
     */
    public static function markAsInactive($url)
    {
        if (is_array($url)) {
            $url = Url::to($url);
        }

        \yii\base\Event::on(static::className(), static::EVENT_RUN, function($event) use($url) {
            $event->sender->setInactive($url);
        });
    }

    public function deleteItemByUrl($url)
    {
        foreach ($this->items as $key => $item) {
            if ($item['url'] == $url) {
                unset($this->items[$key]);
            }
        }
    }
}

?>
