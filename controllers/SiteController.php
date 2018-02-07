<?php

namespace app\controllers;

use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AjaxFilter::className(),
                'only'  => ['user-status-change'],
            ],
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'user-status-change' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    public function actionIndex()
    {
        $user = new User;

        if ($user->load(\Yii::$app->request->post()) && $user->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User is successfully added'));
            $user->setAttribute('login', null);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => User::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'       => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'user' => $user,
        ]);
    }

    /**
     * @throws \yii\base\InvalidParamException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionUserStatusChange()
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $userId = \Yii::$app->request->post('id');
            $status = \Yii::$app->request->post('status');

            if ($status === null) {
                throw new BadRequestHttpException(\Yii::t('exception', '`Status` parameter is required'));
            }

            if (!$userId || !$user = User::findOne($userId)) {
                throw new BadRequestHttpException(\Yii::t('exception', 'User #{id} is not found', [
                    'id' => $userId,
                ]));
            }

            $user->setAttribute('status', $status);

            if (!$user->validate()) {
                return ['status' => 'error', 'errors' => $user->getErrors()];
            }
            if (!$user->save()) {
                throw new Exception(\Yii::t('exception', 'Can not save user #{id}'));
            }

            return [
                'status' => 'success',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'errors' => [
                    [
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage(),
                        'trace'   => $e->getTraceAsString(),
                    ],
                ],
            ];
        }
    }
}
