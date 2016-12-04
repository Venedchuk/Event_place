<?php
	namespace frontend\controllers;

	use frontend\models\Event;
	use Yii;
	use yii\base\InvalidParamException;
	use yii\web\BadRequestHttpException;
	use yii\web\Controller;
	use yii\filters\VerbFilter;
	use yii\filters\AccessControl;
	use common\models\LoginForm;
	use frontend\models\PasswordResetRequestForm;
	use frontend\models\ResetPasswordForm;
	use frontend\models\SignupForm;
	use frontend\models\ContactForm;
	use frontend\models\About;

	/**
	 * Site controller
	 */
	class SiteController extends Controller
	{
		/**
		 * @inheritdoc
		 */
		public function behaviors()
		{
			return [ 'access' => [ 'class' => AccessControl::className(), 'only' => [ 'logout', 'signup' ], 'rules' => [ [ 'actions' => [ 'signup' ], 'allow' => true, 'roles' => [ '?' ], ], [ 'actions' => [ 'logout' ], 'allow' => true, 'roles' => [ '@' ], ], ], ], 'verbs' => [ 'class' => VerbFilter::className(), 'actions' => [ 'logout' => [ 'post' ], ], ], ];
		}

		/**
		 * @inheritdoc
		 */
		public function actions()
		{
			return [ 'error' => [ 'class' => 'yii\web\ErrorAction', ], 'captcha' => [ 'class' => 'yii\captcha\CaptchaAction', 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null, ], ];
		}

		/**
		 * Displays homepage.
		 *
		 * @return mixed
		 */
		public function actionIndex()
		{
			return $this->render('index');
		}

		/**
		 * Logs in a user.
		 *
		 * @return mixed
		 */
		public function actionLogin()
		{
			if(!Yii::$app->user->isGuest) {
				return $this->goHome();
			}

			$model = new LoginForm();
			if($model->load(Yii::$app->request->post()) && $model->login()) {
				return $this->goBack();
			} else {
				return $this->render('login', [ 'model' => $model, ]);
			}
		}

		/**
		 * Logs out the current user.
		 *
		 * @return mixed
		 */
		public function actionLogout()
		{
			Yii::$app->user->logout();

			return $this->goHome();
		}

		/**
		 * Displays contact page.
		 *
		 * @return mixed
		 */
		public function actionContact()
		{
			$model = new ContactForm();
			if($model->load(Yii::$app->request->post()) && $model->validate()) {
				if($model->sendEmail(Yii::$app->params[ 'adminEmail' ])) {
					Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
				} else {
					Yii::$app->session->setFlash('error', 'There was an error sending email.');
				}

				return $this->refresh();
			} else {
				return $this->render('contact', [ 'model' => $model, ]);
			}
		}

		/**
		 * Displays about page.
		 *
		 * @return mixed
		 */
		public function actionAbout()
		{
			$result = "";
			$dist = [ ];
			$state = Yii::$app->request->post('state');
			$stateToDb = Yii::$app->request->post('state');
			if(Yii::$app->request->getBodyParam('state')) {

				$param = Yii::$app->request->post('state');

				$target = $param[ "finish" ];
				$source = $param[ "start" ];
				//echo '<pre>';

				unset($param[ "finish" ]);
				unset($param[ "start" ]);

				$graph_array = array();
				foreach( $param as $key => $value ) {

					$temp = strval($key);
					if($value != "") {
						array_push($graph_array, array( $temp[ 0 ], $temp[ 1 ], $value ));
					}
				}

				$vertices = array();
				$neighbours = array();
				foreach( $graph_array as $edge ) {
					array_push($vertices, $edge[ 0 ], $edge[ 1 ]);
					$neighbours[ $edge[ 0 ] ][] = array( "end" => $edge[ 1 ], "cost" => $edge[ 2 ] );
					$neighbours[ $edge[ 1 ] ][] = array( "end" => $edge[ 0 ], "cost" => $edge[ 2 ] );
				}
				$vertices = array_unique($vertices);

				foreach( $vertices as $vertex ) {
					$dist[ $vertex ] = INF;
					$previous[ $vertex ] = NULL;
				}

				$dist[ $source ] = 0;
				$Q = $vertices;
				while( count($Q) > 0 ) {

					// TODO - Find faster way to get minimum
					$min = INF;
					foreach( $Q as $vertex ) {
						if($dist[ $vertex ] < $min) {
							$min = $dist[ $vertex ];
							$u = $vertex;
						}
					}

					$Q = array_diff($Q, array( $u ));
					if($dist[ $u ] == INF or $u == $target) {
						break;
					}

					if(isset($neighbours[ $u ])) {
						foreach( $neighbours[ $u ] as $arr ) {
							$alt = $dist[ $u ] + $arr[ "cost" ];
							if($alt < $dist[ $arr[ "end" ] ]) {
								$dist[ $arr[ "end" ] ] = $alt;
								$previous[ $arr[ "end" ] ] = $u;
							}
						}
					}
				}
				$path = array();
				$u = $target;
				while( isset($previous[ $u ]) ) {
					array_unshift($path, $u);
					$u = $previous[ $u ];
				}
				array_unshift($path, $u);

				$result = "path is: ".implode(", ", $path)."\n";
				//echo "path is: ".implode(", ", $path)."\n";
				//exit;

				$about = new About();
				$about->state = serialize($stateToDb);
				$about ->save();
				var_dump($stateToDb);
				exit;
			}
			//echo '<pre>';



			return $this->render('about', array( 'result' => $result, 'dist' => $dist, 'state' => $state ));
		}

		/**
		 * Signs user up.
		 *
		 * @return mixed
		 */

		public function actionSignup()
		{
			$model = new SignupForm();
			if($model->load(Yii::$app->request->post())) {
				if($user = $model->signup()) {
					if(Yii::$app->getUser()->login($user)) {
						return $this->goHome();
					}
				}
			}

			return $this->render('signup', [ 'model' => $model, ]);
		}

		/**
		 * Requests password reset.
		 *
		 * @return mixed
		 */
		public function actionRequestPasswordReset()
		{
			$model = new PasswordResetRequestForm();
			if($model->load(Yii::$app->request->post()) && $model->validate()) {
				if($model->sendEmail()) {
					Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

					return $this->goHome();
				} else {
					Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
				}
			}

			return $this->render('requestPasswordResetToken', [ 'model' => $model, ]);
		}

		/**
		 * Resets password.
		 *
		 * @param string $token
		 *
		 * @return mixed
		 * @throws BadRequestHttpException
		 */
		public function actionResetPassword($token)
		{
			try {
				$model = new ResetPasswordForm($token);
			} catch( InvalidParamException $e ) {
				throw new BadRequestHttpException($e->getMessage());
			}

			if($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
				Yii::$app->session->setFlash('success', 'New password was saved.');

				return $this->goHome();
			}

			return $this->render('resetPassword', [ 'model' => $model, ]);
		}
	}
