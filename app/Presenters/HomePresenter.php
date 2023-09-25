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
		->limit(15)->fetchAll();
}
    
protected function createComponentCalculatorForm()
{
    $form = new \Nette\Application\UI\Form;

    $form->addText('marze', 'MARŽE')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('profit', 'Hrubý zisk')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('google', 'Google Ads')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('meta', 'Meta')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('bing', 'Bing')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('sklik', 'Sklik')->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč');
    $form->addText('z_vydejni', 'Výdejní místo')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('z_doruky', 'Do ruky')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('p_vydejni', 'Výdejní místo')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('p_doruky', 'Do ruky')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('p_balikovna', 'Balíkovna')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('ppl_vydejni', 'Výdejní místo')->setHtmlAttribute('placeholder', 'Zadejte počet');
    $form->addText('ppl_doruky', 'Do ruky')->setHtmlAttribute('placeholder', 'Zadejte počet');
    
    $form->addSubmit('submit', 'Vypočítat');

    $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];
   

    return $form;
}

public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
{
    $marzeValue = (float)$values->profit - ((float)$values->google + (float)$values->meta + (float)$values->bing + (float)$values->sklik + (float)$values->doprava);

    $this->database->table('values')->insert([
        'marze' => $marzeValue,
        'profit' => $values->profit,
        'google' => $values->google,
        'meta' => $values->meta,
        'bing' => $values->bing,
        'sklik' => $values->sklik,
        'z-vydejni' => $values->z_vydejni,
        'z-doruky' => $values->z_doruky,
        'p-vydejni' => $values->p_vydejni,
        'p-doruky' => $values->p_doruky,
        'p-balikovna' => $values->p_balikovna,
        'ppl-vydejni' => $values->ppl_vydejni,
        'ppl-doruky' => $values->ppl_doruky,
        'created_at' =>new \DateTime()
    ]);


        

    $form['marze']->setValue($marzeValue);

    $session = $this->getSession('form');
    $session->formValues = $values;


    $this->redirect('this');
}

}









