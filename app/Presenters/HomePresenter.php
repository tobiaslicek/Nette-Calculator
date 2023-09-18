<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{
protected function createComponentCalculatorForm()
{
    $form = new \Nette\Application\UI\Form;

    $form->addText('marze', 'MARŽE');
    $form->addText('profit', 'Hrubý zisk');
    $form->addText('reklamy', 'Reklamy');
    $form->addText('doprava', 'Doprava');

    $form->addSubmit('submit', 'Vypočítat');

    $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

    return $form;
}
public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
{
    $marzeValue = $values->profit - ($values->reklamy + $values->doprava);
    $form['marze']->setValue($marzeValue);
}
}



/* 
    Verze 1

    marže = hrubý zisk - Reklamy - Doprava
    
    
    
    
    Verze 2
    
    marže = hrubý zisk - Google Ads - Meta - Bing - Sklik - Zásilkovna - Pošta - PPL 


	<div class="ads">
		<p class="ads__p"> - Reklamy</p>
			<div class="google">
				<p class="ads-google">Google Ads</p>
				<input type="text" name="num1" placeholder="">
			</div>
			<div class="google">
				<p class="ads-meta">Meta</p>
				<input type="text" name="num1" placeholder="">
				</div>
			<div class="google">
				<p class="ads-bing">Bing</p>
				<input type="text" name="num1" placeholder="">
				</div>
			<div class="google">
				<p class="ads-sklik">Sklik</p>
				<input type="text" name="num1" placeholder="">
			</div>
	</div>


	<div class="carrier">
		<p class="carrier__p"> - Dopravce</p>
			<div class="zasilkovna">
				<h2 class="carrier-zasilkovna"><strong>Zásilkovna</strong></h2>
				<p class="carrier-option">Výdejní místo</p>
				<input type="text" name="num1" placeholder="">
				<p class="carrier-option">Do ruky</p>
				<input type="text" name="num1" placeholder="">
			</div>
			<div class="posta">
				<h2 class="carrier-posta"><strong>Pošta</strong></h2>
				<p class="carrier-option">Výdejní místo</p>
				<input type="text" name="num1" placeholder="">
				<p class="carrier-option">Do ruky</p>
				<input type="text" name="num1" placeholder="">
				<p class="carrier-option">Balíkovna</p>
				<input type="text" name="num1" placeholder="">
				</div>
			<div class="ppl">
				<h2 class="carrier-ppl"><strong>PPL</strong></h2>
				<p class="carrier-option">Výdejní místo</p>
				<input type="text" name="num1" placeholder="">
				<p class="carrier-option">Do ruky</p>
				<input type="text" name="num1" placeholder="">
				</div>
	</div>
	<p class="order__p">Objednávek celkem</p>
		<input type="text" name="num1" placeholder="celkem">













    marže = hrubý zisk - $reklamy - poštovné

    $reklamy = Google Ads + Meta + Bing + Sklik
    $poštovné = Zásilkovna + Pošta + PPL 


*/