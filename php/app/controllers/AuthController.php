<?php

namespace app\controllers;
use webvimark\modules\UserManagement\models\User;
use yii\rest\Controller;
use webvimark\modules\UserManagement\models\forms\LoginForm;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionLogin() {
        $model = new LoginForm();
        $model->load(\Yii::$app->request->getBodyParams(), '');
        if ($model->login()) {
            $tokenString = $this->generateToken();
            return ['token' => $tokenString];
        } else {
            throw new UnauthorizedHttpException('Falha ao autenticar. Verifique suas credenciais.');
        }
    }

    private function getRefreshToken($user, $impersonator = null) {
        $model= User::findIdentity(\Yii::$app->user->id);
        $model->auth_key = \Yii::$app->security->generateRandomString(32);
        if (!$model->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $model->getErrorSummary(true));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $model->auth_key,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/auth/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));

        return $model->auth_key;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function generateToken()
    {
        $user = \Yii::$app->user->identity;
        $now = new \DateTimeImmutable('now', new \DateTimeZone(\Yii::$app->timeZone));
        $token = \Yii::$app->jwt->getBuilder()
            // Configures the time that the token was issued
            ->issuedAt($now)
            // Configures the time that the token can be used
            ->canOnlyBeUsedAfter($now)
            // Configures the expiration time of the token
            ->expiresAt($now->modify('+1 hour'))
            // Configures a new claim, called "uid", with user ID, assuming $user is the authenticated user object
            ->withClaim('uid', $user->id)
            // Builds a new token
            ->getToken(
                \Yii::$app->jwt->getConfiguration()->signer(),
                \Yii::$app->jwt->getConfiguration()->signingKey()
            );
        $tokenString = $token->toString();
        return $tokenString;
    }
}