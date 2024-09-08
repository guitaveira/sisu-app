<?php

class FeedbackCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1);

    }

    public function sendFeedbackSuccessfully(\FunctionalTester $I)
    {
        $I->amOnRoute('feedback/create');
        $I->submitForm('form', [
            'Feedback[nome]' => 'Alessandro',
            'Feedback[email]' => 'asdsd@asdasd.com',
            'Feedback[idade]' => '23',
            'Feedback[feedback]' => 'Nda a delcarar',
        ]);
        $I->expectTo('Encontrar registro na base de Dados');
        $result=$I->seeRecord('\app\models\Feedback',[
            'nome' => 'Alessandro',
            'email' => 'asdsd@asdasd.com',
            'idade' => '23',
            'feedback' => 'Nda a delcarar',
        ]);
    }

    public function errorEmailonAddFeedback(\FunctionalTester $I)
    {
        $I->amOnRoute('feedback/create');
        $I->submitForm('form', [
            'Feedback[nome]' => 'Alessandro',
            'Feedback[email]' => 'asdsd@asdifgasd.com',
            'Feedback[idade]' => '23',
            'Feedback[feedback]' => 'Nda a delcarar',
        ]);
        $I->expectTo('Não encontrar registro salvo e erro sendo exibido');
        $result=$I->dontSeeRecord('\app\models\Feedback',[
            'nome' => 'Alessandro',
            'email' => 'asdsd@asdifgasd.com',
            'idade' => '23',
            'feedback' => 'Nda a delcarar',
        ]);
        $I->seeElement('.help-block:contains("não é um endereço de e-mail válido")');
    }
}