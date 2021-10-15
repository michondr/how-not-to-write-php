<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    public function renderHello(string $text=''): void {
        $this->template->text = $text;
    }

    public function actionData(): void {
        $data = ['hello' => 'nette'];
        $this->sendJson($data);
    }
}
