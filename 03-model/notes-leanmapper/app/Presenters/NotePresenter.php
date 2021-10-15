<?php

declare(strict_types=1);
namespace App\Presenters;

use App\Model\Entities\Category;
use App\Model\Entities\Note;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\NoteFacade;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

class NotePresenter extends \Nette\Application\UI\Presenter {

    /** @var NoteFacade $noteFacade */
    private /*NoteFacade*/ $noteFacade;

    /** @var CategoriesFacade $categoriesFacade */
    private /*CategoriesFacade*/ $categoriesFacade;

  public function actionDefault(){
    $this->redirect('list');
  }

  /**
   * Akce pro zobrazení seznamu dostupných kategorií
   */
  public function renderList(){
      $this->template->notes= $this->noteFacade->findNotes(['order' => 'title']);

  }

  /**
   * Akce pro zobrazení detailů jedné poznámky
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderShow(int $id):void {
//    try{
      $this->template->note=$this->noteFacade->getNote($id);
//    }catch (\Exception $e){
//      $this->error('Požadovaná kategorie nebyla nalezena', 404);
//    }
  }
  /**
   * Akce pro úpravu jedné noty
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderEdit(int $id):void {
      try{
      $note=$this->noteFacade->getNote($id);
    }catch (\Exception $e){
      $this->error('Požadovaná kategorie nebyla nalezena', 404);
    }
    $form=$this->getComponent('noteEditForm');
    $form->setDefaults([
      'note_id'=>$note->noteId,
      'title'=>$note->title,
      'category'=>$note->category,
      'text'=>$note->text
    ]);
    $this->template->note=$note;
  }

  /**
   * Formulář na editaci kategorií
   * @return Form
   */
  public function createComponentNoteEditForm():Form {
    $form = new Form();
    $form->addHidden('note_id');
    $form->addText('title','Název poz')->setRequired('Musíte zadat název poz');
    $form->addTextArea('text','Popis poz')->setRequired(false);
    $form->addSelect('category','Kategorie poznámky', $this->getCategoriesAsStringArray())->setRequired(false);
    $form->addSubmit('save','uložit')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        //hodnoty z formuláře získáme v podobě pole
        /** @var array $values */
        $values=$button->form->getValues(true);

        //provedení potřebné akce
        if (!empty($values['note_id'])){
            $note = $this->noteFacade->getNote((int)$values['note_id']);

        }else{
            $note = new Note();
        }

        $note->text = $values['text'];
        $note->title = $values['title'];
        $note->category = $this->categoriesFacade->getCategory($values['category']);

        $this->noteFacade->saveNote($note);

        //přesměrování na seznam kategorií
        $this->redirect('list');
      };
    $form->addSubmit('storno','zrušit')
      ->setHtmlAttribute('class','btn btn-light')
      ->setValidationScope([])
      ->onClick[]=function(SubmitButton $button){
        $this->redirect('list');
      };
    return $form;
  }







  public function injectAll(NoteFacade $noteFacade, CategoriesFacade $categoriesFacade){
    $this->noteFacade=$noteFacade;
    $this->categoriesFacade=$categoriesFacade;
  }

    /**
     * @return array
     */
    private function getCategoriesAsStringArray(): array
    {
        $categoryIdToText = [];

        foreach ($this->categoriesFacade->findCategories() as $category){
            $categoryIdToText[$category->categoryId] = 'název: '.$category->title;
        }

        return $categoryIdToText;
    }
}
