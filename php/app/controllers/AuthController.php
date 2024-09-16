<?php

namespace app\controllers;
use yii\rest\Controller;
use webvimark\modules\UserManagement\models\forms\LoginForm;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{
    public function actionLogin() {
        $model = new LoginForm();
        $model->load(\Yii::$app->request->getBodyParams(), '');
        if ($model->login()) {
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

            return ['token' => $tokenString];
        } else {
            throw new UnauthorizedHttpException('Falha ao autenticar. Verifique suas credenciais.');
        }
    }
}