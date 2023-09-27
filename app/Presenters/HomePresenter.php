<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Table\Selection;


final class HomePresenter extends Nette\Application\UI\Presenter
{

    private array $control;

    public function beforeRender()
    {
        $this->template->addFilter('money', fn (?float $amount)
        => ($amount ? number_format($amount, 2, ",", "&nbsp;") . "&nbsp;Kč" : "-"));
    }

    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $control = $this->database
            ->table('values')
            ->select('*, zasilkovna_pickup_point + zasilkovna_hand_delivery + posta_pickup_point + posta_hand_delivery + posta_balikovna + ppl_pickup_point + ppl_hand_delivery AS check_value')
            ->order('created_at DESC')
            ->limit(50)->fetchAll();

        $this->template->items = $control;
    }

    protected function createComponentCalculatorForm()
    {
        $form = new \Nette\Application\UI\Form;

        $form->addText('profit', 'Hrubý zisk')
            ->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč')
            ->setRequired('Prosím, vyplňte pole "Hrubý zisk".');
        $form->addText('google', 'Google Ads')
            ->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč')
            ->setNullable();
        $form->addText('meta', 'Meta')
            ->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč')
            ->setNullable();
        $form->addText('bing', 'Bing')
            ->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč')
            ->setNullable();
        $form->addText('sklik', 'Sklik')
            ->setHtmlAttribute('placeholder', 'Zadejte hodnotu v Kč')
            ->setNullable();
        $form->addText('zasilkovna_pickup_point', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('zasilkovna_hand_delivery', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('posta_pickup_point', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('posta_hand_delivery', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('posta_balikovna', 'Balíkovna')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('ppl_pickup_point', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('ppl_hand_delivery', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('total', 'Objednávek celkem')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setRequired('Prosím, vyplňte pole "Objednávek celkem"');

        $form->addSubmit('submit', 'Vypočítat');

        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

        return $form;
    }

    public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
    {
        $marginValue = (float)$values->profit
            - ((float)$values->google
                + (float)$values->meta
                + (float)$values->bing
                + (float)$values->sklik
                + (((int)$values->zasilkovna_pickup_point * 79)
                    + ((int)$values->zasilkovna_hand_delivery * 115)
                    + ((int)$values->posta_pickup_point * 90)
                    + ((int)$values->posta_hand_delivery * 130)
                    + ((int)$values->posta_balikovna * 65)
                    + ((int)$values->ppl_pickup_point * 60)
                    + ((int)$values->ppl_hand_delivery * 99)));

        $soucet_dopravci = (int)$values->zasilkovna_pickup_point + (int)$values->zasilkovna_hand_delivery + (int)$values->posta_pickup_point + (int)$values->posta_hand_delivery + (int)$values->posta_balikovna + (int)$values->ppl_pickup_point + (int)$values->ppl_hand_delivery;

        $this->database->table('values')->insert([
            'margin' => $marginValue,
            'profit' => $values->profit,
            'google' => $values->google,
            'meta' => $values->meta,
            'bing' => $values->bing,
            'sklik' => $values->sklik,
            'zasilkovna_pickup_point' => $values->zasilkovna_pickup_point,
            'zasilkovna_hand_delivery' => $values->zasilkovna_hand_delivery,
            'posta_pickup_point' => $values->posta_pickup_point,
            'posta_hand_delivery' => $values->posta_hand_delivery,
            'posta_balikovna' => $values->posta_balikovna,
            'ppl_pickup_point' => $values->ppl_pickup_point,
            'ppl_hand_delivery' => $values->ppl_hand_delivery,
            'total' => $values->total,
            'created_at' => new \DateTime()
        ]);

        if ($soucet_dopravci != $values->total) {
            $this->flashMessage('Zadaný počet objednávek celkem je rozdílný od kontrolního součtu', 'danger');
        }

        $this->redirect('this');
    }
}
