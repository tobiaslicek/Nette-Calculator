<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form; //přidáno správně? nebo smazat?


final class HomePresenter extends Nette\Application\UI\Presenter
{

public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

public function renderDefault(): void
{
	$this->template->items = $this->database
		->table('values')
		->order('created_at DESC')
		->limit(5)->fetchAll();
}
    
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

    $this->database->table('values')->insert([
        'marze' => $marzeValue,
    ]);

    $form['marze']->setValue($marzeValue);

    $session = $this->getSession('form');
    $session->formValues = $values;

    $this->redirect('this');
}

}









