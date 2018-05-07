<?php

namespace app\controllers;

use app\components\YandexDirect\BidService;
use Yii;
use app\models\Bid;
use app\models\BidSearch;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BidController implements the CRUD actions for Bid model.
 */
class BidController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionChecked()
    {
        if ($id = Yii::$app->request->get('id')) {
            try {
                $bid = (new BidService())->checkedBid($id);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage());
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $bid
            ]);

            return $this->render('checked_info', [
                'dataProvider' => $dataProvider,
                'model' => $this->findModel($id),
            ]);
        }

        throw new NotFoundHttpException('The requested page required id group');
    }

    /**
     * Lists all Bid models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BidSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bid model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Bid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bid::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
