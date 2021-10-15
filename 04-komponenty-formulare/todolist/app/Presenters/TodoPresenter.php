<?php

namespace App\Presenters;

use App\Components\TodoEditForm\TodoEditForm;
use App\Components\TodoEditForm\TodoEditFormFactory;
use App\Model\Facades\TodosFacade;
use Nette\Application\UI\Presenter;

/**
 * Class TodoPresenter
 * @package App\Presenters
 */
class TodoPresenter extends Presenter
{
    /** @var TodosFacade $todosFacade */
    private $todosFacade;
    /** @var TodoEditFormFactory $todoEditFormFactory */
    private $todoEditFormFactory;

    public function __construct(
        TodosFacade $todosFacade
    )
    {
        $this->todosFacade = $todosFacade;
    }

    public function renderDefault()
    {
        $this->template->todosUnfinished = $this->todosFacade->findTodos(['order'=> 'deadline asc', 'completed' => '0']);
        $this->template->todosFinished = $this->todosFacade->findTodos(['order'=> 'deadline asc', 'completed' => '1']);
    }

    public function renderShow(int $id)
    {
        $this->template->todo = $this->todosFacade->getTodo($id);
    }

    public function handleComplete(int $id)
    {
        $todo = $this->todosFacade->getTodo($id);
        $todo->completed = !$todo->completed;

        $this->todosFacade->saveTodo($todo);
    }

    public function renderEdit(int $id)
    {
        $todo = $this->todosFacade->getTodo($id);
        $form=$this->getComponent('todoEditForm');
        $form->setDefaults($todo);

        $this->template->todo = $todo;
    }

    public function renderAdd()
    {
        $form=$this->getComponent('todoEditForm');
    }

    public function createComponentTodoEditForm():TodoEditForm {
        $form=$this->todoEditFormFactory->create();
        $form->onFinished[]=function(string $message=''){
            if (!empty($message)){
                $this->flashMessage($message);
            }
            $this->redirect('default');
        };
        $form->onFailed[]=function(string $message=''){
            if (!empty($message)){
                $this->flashMessage($message,'error');
            }
            $this->redirect('default');
        };
        $form->onCancel[]=function(){
            $this->redirect('default');
        };
        return $form;
    }


    /**
     * @param TodoEditFormFactory $todoEditFormFactory
     */
    public function injectTodoEditFormFactory(TodoEditFormFactory $todoEditFormFactory):void {
        $this->todoEditFormFactory=$todoEditFormFactory;
    }
}