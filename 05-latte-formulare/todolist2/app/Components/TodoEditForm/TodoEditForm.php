<?php

namespace App\Components\TodoEditForm;

use App\Model\Entities\Todo;
use App\Model\Facades\TodosFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\SmartObject;

/**
 * Class TodoEditForm
 * @package App\Components\TodoEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class TodoEditForm extends Form
{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onFailed */
    public $onFailed = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];

    /** @var TodosFacade $todosFacade */
    private $todosFacade;

    public function __construct(
        Nette\ComponentModel\IContainer $parent = null,
        string $name = null,
        TodosFacade $todosFacade
    )
    {
        parent::__construct($parent, $name);
        $this->todosFacade = $todosFacade;

        $this->createSubs();
    }

    /**
     * @param Todo|array|object $values
     * @param bool $erase
     * @return $this
     */
    public function setDefaults($values, bool $erase = false): self
    {
        if ($values instanceof Todo) {
            $todoValues = [
                'todoId' => $values->todoId,
                'title' => $values->title,
                'description' => $values->description,
                'deadline' => $values->deadline !== null ? $values->deadline->format('Y-m-d') : null,
                'completed' => $values->completed ? 'on' : '',
//                'tags' => array_flip($this->getTagsData($values->tags)),
            ];
            parent::setDefaults($todoValues, $erase);
        }

        return $this;
    }

    private function createSubs()
    {
        $todoId = $this
            ->addHidden('todoId');

        $this
            ->addText('title', 'název')
            ->setRequired(true);
        $this
            ->addTextArea('description', 'popis')
            ->setRequired(false);

        $this
            ->addSubmit('save', 'uložit')
            ->onClick[] = function () {
            $values = $this->getValues('array');

            if (empty($values['todoId'])) {
                $todo = new Todo();
            } else {
                try {
                    $todo = $this->todosFacade->getTodo($values['todoId']);
                } catch (\Exception $e) {
                    $this->onFailed('not found');

                    return;
                }
            }

            $todo->title = $values['title'];
            $todo->description = $values['description'];

            $this->todosFacade->saveTodo($todo);

            $this->setValues(['todoId' => $todo->todoId]);
            $this->onFinished();
        };

        $this
            ->addSubmit('cancel', 'zrušit')
            ->setValidationScope([$todoId])
            ->onClick[] = function () {
            $this->onCancel();
        };
    }

}