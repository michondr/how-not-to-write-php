<?php

namespace App\Presenters;

use App\Components\TodoEditForm\TodoEditForm;
use App\Components\TodoEditForm\TodoEditFormFactory;
use App\Model\Facades\TodosFacade;
use Nette;
use Nette\Application\UI\Presenter;

/**
 * Class TodoPresenter
 * @package App\Presenters
 */
class TodoPresenter extends Presenter
{
    /** @var TodosFacade $todosFacade */
    private $todosFacade;

    /** @var TodoEditFormFactory $todosEditFormFactory */
    private $todosEditFormFactory;

    public function renderDefault()
    {
        //TODO tady to bude chtít v rámci plnění úkolů na cvičení nějaké změny
        $this->template->todos = $this->todosFacade->findTodos();
    }

    public function renderEdit(int $toodId)
    {
        $todo = $this->todosFacade->getTodo($toodId);

        $form = $this->getComponent('todoEditForm');
        $form->setDefaults($todo);

        $this->template->todo = $todo;
    }

    public function createComponentTodoEditForm(): TodoEditForm
    {
        $form = $this->todosEditFormFactory->create();

        $form->onCancel[] = function () {
            $this->redirect('default');
        };
        $form->onError[] = function () use ($form) {
            $this->flashMessage('broken', 'error');
            $this->redirect('default');
        };
        $form->onFinished[] = function () {
            $this->flashMessage('success', 'success');
            $this->redirect('default');
        };

        //do stuff

        return $form;
    }

    public function injectTodosFacade(TodosFacade $todosFacade)
    {
        $this->todosFacade = $todosFacade;
    }

    public function injectTodoEditFormFactory(TodoEditFormFactory $formFactory)
    {
        $this->todosEditFormFactory = $formFactory;
    }
}