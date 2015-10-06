<?php
/**
 * ClearController
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 08.11.2014
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin\controllers;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\modules\admin\actions\AdminAction;
use skeeks\cms\modules\admin\controllers\helpers\rules\NoModel;
use skeeks\sx\Dir;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class IndexController
 * @package skeeks\cms\modules\admin\controllers
 */
class ClearController extends AdminController
{
    public function init()
    {
        $this->name = "Удаление временных файлов";
        parent::init();
    }

    public function actions()
    {
        return
        [
            "index" =>
            [
                "class"        => AdminAction::className(),
                "name"         => "Чистка временных данных",
                "callback"     => [$this, 'actionIndex'],
            ],
        ];
    }

    public function actionIndex()
    {
        $clearDirs =
        [
            [
                'label'     => 'common временные файлы',
                'dir'       => new Dir(\Yii::getAlias('@common/runtime'), false)
            ],

            [
                'label'     => 'console временные файлы',
                'dir'       => new Dir(\Yii::getAlias('@console/runtime'), false)
            ],


            [
                'label'     => 'runtime (текущий сайт)',
                'dir'       => new Dir(\Yii::getAlias('@runtime'), false)
            ],

            [
                'label'     => 'Файлы кэша (текущий сайт)',
                'dir'       => new Dir(\Yii::getAlias('@runtime/cache'), false)
            ],

            [
                'label'     => 'Файлы дебаг информации (текущий сайт)',
                'dir'       => new Dir(\Yii::getAlias('@runtime/debug'), false)
            ],

            [
                'label'     => 'Файлы логов (текущий сайт)',
                'dir'       => new Dir(\Yii::getAlias('@runtime/logs'), false)
            ],

            /*[
                'label'     => 'Временные js и css файлы (текущий сайт)',
                'dir'       => new Dir(\Yii::getAlias('@app/web/assets'), false)
            ]*/

        ];

        $rr = new RequestResponse();
        if ($rr->isRequestAjaxPost())
        {
            foreach ($clearDirs as $data)
            {
                $dir = ArrayHelper::getValue($data, 'dir');
                if ($dir instanceof Dir)
                {
                    if ($dir->isExist())
                    {
                        $dir->clear();
                    }
                }
            }

            \Yii::$app->db->getSchema()->refresh();
            \Yii::$app->cache->flush();
            \Yii::$app->cms->generateModulesConfigFile();

            $rr->success = true;
            $rr->message = 'Кэш очищен';
            return $rr;
        }

        return $this->render('index', [
            'clearDirs'     => $clearDirs,
        ]);
    }


}