<?php

declare(strict_types = 1);

namespace App\Presenters;

use Nette;

final class DemoPresenter extends Nette\Application\UI\Presenter
{
    public function renderSum(int $a, int $b): void
    {
        $this->template->a = $a;
        $this->template->b = $b;
        $this->template->sum = $a+$b;
    }
}
