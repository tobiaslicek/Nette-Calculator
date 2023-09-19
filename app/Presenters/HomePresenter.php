<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{
// protected function createComponentCalculatorForm()
// {
//     $form = new \Nette\Application\UI\Form;

//     $form->addText('marze', 'MARŽE');
//     $form->addText('profit', 'Hrubý zisk');
//     $form->addText('reklamy', 'Reklamy');
//     $form->addText('doprava', 'Doprava');

//     $form->addSubmit('submit', 'Vypočítat');

//     $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

//     return $form;
// }

// public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
// {
//     $marzeValue = $values->profit - ($values->reklamy + $values->doprava);
//     $form['marze']->setValue($marzeValue);
// }

protected function createComponentCalculatorForm()
{
    $form = new \Nette\Application\UI\Form;

    $form->addText('marze', 'MARŽE');
    $form->addText('profit', 'Hrubý zisk');
    $form->addText('google', 'Google Ads');
    $form->addText('meta', 'Meta');
    $form->addText('bing', 'Bing');
    $form->addText('sklik', 'Sklik');
    $form->addText('doprava', 'Doprava');

    $form->addSubmit('submit', 'Vypočítat');

    $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

    $session = $this->getSession('form');

    if (isset($session->formValues->marze)) {
        $form['marze']->setValue($session->formValues->marze);
    }

    return $form;
}

public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
{
 
    $marzeValue = (float)$values->profit - ((float)$values->google + (float)$values->meta + (float)$values->bing + (float)$values->sklik + (float)$values->doprava);

    $form['marze']->setValue($marzeValue);

    $session = $this->getSession('form');
    $session->formValues = $values;

    // $this->redirect('this');
}

}


/*







*/

