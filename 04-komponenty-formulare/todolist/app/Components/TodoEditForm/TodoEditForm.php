<?php

namespace App\Components\TodoEditForm;

use App\Model\Entities\Tag;
use App\Model\Entities\Todo;
use App\Model\Facades\TagsFacade;
use App\Model\Facades\TodosFacade;
use Dibi\DateTime;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
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
    /** @var TagsFacade $tagsFacade */
    private $tagsFacade;

    /**
     * TodoEditForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param TodosFacade $todosFacade
     */
    public function __construct(
        Nette\ComponentModel\IContainer $parent = null,
        string $name = null,
        TodosFacade $todosFacade,
        TagsFacade $tagsFacade
    )
    {
        parent::__construct($parent, $name);
        $this->todosFacade = $todosFacade;
        $this->tagsFacade = $tagsFacade;
        $this->createSubcomponents();
    }

    /**
     * Metoda pro nastavení výchozích hodnot formuláře
     * @param Todo|array|object $values
     * @param bool $erase = false
     * @return $this
     */
    public function setDefaults($values, bool $erase = false): self
    {
        if ($values instanceof Todo) {
            $values = [
                'todoId' => $values->todoId,
                'title' => $values->title,
                'description' => $values->description,
                'deadline' => $values->deadline !== null ? $values->deadline->format('Y-m-d') : null,
                'completed' => $values->completed ? 'on' : '',
                'tags' => array_flip($this->getTagsData($values->tags)), //bacha na tohle - default chce for some inexplicable reason naopak hozený value->label
            ];
        }
        parent::setDefaults($values, $erase);

        return $this;
    }

    /**
     * Metoda vytvářející vnitřní strukturu formuláře
     */
    private function createSubcomponents(): void
    {
        $this->addProtection('Opakujte prosím odeslání formuláře znovu.');
        $todoId = $this->addHidden('todoId');
        $this->addText('title', 'title')->setRequired('Zadejte název todou!');
        $this->addText('description', 'description')->setRequired('Zadejte description todou!');
        $this->addText('deadline', 'deadline')->setHtmlType('date');
        $this->addText('completed', 'completed')->setHtmlType('checkbox');
        $this->addMultiSelect('tags', 'štítky', $this->getTagsData($this->tagsFacade->findTags()));
        $this->addSubmit('save', 'uložit')
            ->onClick[] = function (SubmitButton $submitButton) {
            #region akce pro save
            $values = $this->getValues('array');
            if (!empty($values['todoId'])) {
                //chceme najít existující todo podle jeho ID
                try {
                    $todo = $this->todosFacade->getTodo($values['todoId']);
                } catch (\Exception $e) {
                    $this->onFailed('Todo nebyl nalezen.');

                    return;
                }
            } else {
                //chceme vytvořit nový todo
                $todo = new Todo();
            }
//            echo "<pre>";
//            print_r(array_values($values['tags']));
//            print_r(count($tagsFacade->findTagsByIds(array_values($values['tags']))));
//            die;
            $todo->title = $values['title'];
            $todo->description = $values['description'];
            $todo->deadline = empty($values['deadline']) ? null : new DateTime($values['deadline']);
            $todo->completed = empty($values['completed']) ? false : true;
//            $todo->tags = $tagsFacade->findTagsByIds(array_values($values['tags'])); //Tohle nejde proč jako sakra?!

            $this->todosFacade->saveTodo($todo); //fix, nemůžu přidávat tady když nemám uložené todo

            foreach ($this->tagsFacade->findTagsByIds(array_values($values['tags'])) as $chosenTag){
                $chosenTag->addToTodos($todo);
                $this->tagsFacade->saveTag($chosenTag);
            }

            $this->setValues(['todoId' => $todo->todoId]);
            $this->onFinished('Todo byl uložen.');
            #endregion akce pro save
        };
        $this->addSubmit('cancel', 'zrušit')
            ->setValidationScope([$todoId])
            ->onClick[] = function () {
            #region akce pro cancel
            $this->onCancel();
            #endregion akce pro cancel
        };
    }

    private function getTagsData(array $tags): array
    {
        $tagsData = [];

        foreach ($tags as $tag) {
            $tagsData[$tag->tagId] = 'id:'.$tag->tagId.' title: ' . $tag->title;
        }

        return $tagsData;
    }

}