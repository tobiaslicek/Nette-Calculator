<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Table\Selection;


final class HomePresenter extends Nette\Application\UI\Presenter
{

    private array $kontrola;

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
        $kontrola = $this->database
            ->table('values')
            ->select('*, z_vydejni + z_doruky + p_vydejni + p_doruky + p_balikovna + ppl_vydejni + ppl_doruky AS check_value')
            ->order('created_at DESC')
            ->limit(50)->fetchAll();

        $this->template->items = $kontrola;
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
        $form->addText('z_vydejni', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('z_doruky', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('p_vydejni', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('p_doruky', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('p_balikovna', 'Balíkovna')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('ppl_vydejni', 'Výdejní místo')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('ppl_doruky', 'Do ruky')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setNullable();
        $form->addText('celkem', 'Objednávek celkem')
            ->setHtmlAttribute('placeholder', 'Zadejte počet')
            ->setRequired('Prosím, vyplňte pole "Objednávek celkem"');

        $form->addSubmit('submit', 'Vypočítat');

        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

        return $form;
    }

    public function calculatorFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values)
    {
        $marzeValue = (float)$values->profit
            - ((float)$values->google
                + (float)$values->meta
                + (float)$values->bing
                + (float)$values->sklik
                + (((int)$values->z_vydejni * 79)
                    + ((int)$values->z_doruky * 115)
                    + ((int)$values->p_vydejni * 90)
                    + ((int)$values->p_doruky * 130)
                    + ((int)$values->p_balikovna * 65)
                    + ((int)$values->ppl_vydejni * 60)
                    + ((int)$values->ppl_doruky * 99)));

        $soucet_dopravci = (int)$values->z_vydejni + (int)$values->z_doruky + (int)$values->p_vydejni + (int)$values->p_doruky + (int)$values->p_balikovna + (int)$values->ppl_vydejni + (int)$values->ppl_doruky;

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
            'created_at' => new \DateTime()
        ]);

        if ($soucet_dopravci != $values->celkem) {
            $this->flashMessage('Zadaný počet objednávek celkem je rozdílný od kontrolního součtu', 'danger');
        }

        $this->redirect('this');
    }
}
