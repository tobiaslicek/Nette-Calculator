<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form; 


final class HomePresenter extends Nette\Application\UI\Presenter
{

public function beforeRender() {
    $this->template->addFilter('money', fn(?float $amount) => 
    ($amount ? number_format($amount, 2, ",", "&nbsp;")."&nbsp;Kč" : "-"));
}    

public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

public function renderDefault(): void
{
    $kontrola = $this->database
		->table('values')
        ->select('*, z_vydejni + z_doruky + p_vydejni + p_doruky + p_balikovna + ppl_vydejni + ppl_doruky AS check_value')
		->order('created_at DESC')
		->limit(50)->fetchAll();
\Tracy\Debugger::barDump($kontrola);

	$this->template->items = $kontrola;
}
    
protected function createComponentCalculatorForm()
{
    $form = new \Nette\Application\UI\Form;

    $form->addText('marze', 'MARŽE')->setHtmlAttribute('placeholder', 'Toto pole nechte volné');
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
    $form->addText('celkem', 'Objednávek celkem')->setHtmlAttribute('placeholder', 'Toto pole nechte volné');
    
    $form->addSubmit('submit', 'Vypočítat');

    $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];
   

    return $form;
}

public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
{
     $marzeValue = (float)$values->profit - ((float)$values->google + (float)$values->meta + (float)$values->bing + (float)$values->sklik + (((int)$values->z_vydejni * 79) + ((int)$values->z_doruky * 115) + ((int)$values->p_vydejni * 90) + ((int)$values->p_doruky * 130) + ((int)$values->p_balikovna * 65) + ((int)$values->ppl_vydejni * 60) + ((int)$values->ppl_doruky * 99)));
 

    $this->database->table('values')->insert([
        'marze' => $marzeValue,
        'profit' => $values->profit,
        'google' => $values->google,
        'meta' => $values->meta,
        'bing' => $values->bing,
        'sklik' => $values->sklik,
        'z_vydejni' => $values->z_vydejni,
        'z_doruky' => $values->z_doruky,
        'p_vydejni' => $values->p_vydejni,
        'p_doruky' => $values->p_doruky,
        'p_balikovna' => $values->p_balikovna,
        'ppl_vydejni' => $values->ppl_vydejni,
        'ppl_doruky' => $values->ppl_doruky,
        'celkem' => $values->celkem,
        'created_at' =>new \DateTime()
    ]);


        

    $form['marze']->setValue($marzeValue);

    $session = $this->getSession('form');
    $session->formValues = $values;


    $this->redirect('this');
}

}









