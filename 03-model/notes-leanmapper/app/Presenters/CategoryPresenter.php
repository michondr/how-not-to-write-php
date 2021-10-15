<?php

declare(strict_types=1);
namespace App\Presenters;

use App\Model\Entities\Category;
use App\Model\Facades\CategoriesFacade;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

class CategoryPresenter extends \Nette\Application\UI\Presenter {
  /** @var CategoriesFacade $categoriesFacade */
  private /*CategoriesFacade*/ $categoriesFacade;

  public function actionDefault(){
    $this->redirect('list');
  }

  /**
   * Akce pro zobrazení seznamu dostupných kategorií
   */
  public function renderList(){
      $ategories = $this->categoriesFacade->findCategories(['order' => 'title']);
      $this->template->categories= $ategories;
  }

  /**
   * Akce pro zobrazení detailů jedné kategorie
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderShow(int $id):void {
//    try{
      $this->template->category=$this->categoriesFacade->getCategory($id);
//    }catch (\Exception $e){
//      $this->error('Požadovaná kategorie nebyla nalezena', 404);
//    }
  }
  /**
   * Akce pro úpravu jedné kategorie
   * @param int $id
   * @throws \Nette\Application\BadRequestException
   */
  public function renderEdit(int $id):void {
      try{
      $category=$this->categoriesFacade->getCategory($id);
    }catch (\Exception $e){
      $this->error('Požadovaná kategorie nebyla nalezena', 404);
    }
    $form=$this->getComponent('categoryEditForm');
    $form->setDefaults([
      'category_id'=>$category->categoryId,
      'title'=>$category->title,
      'description'=>$category->description
    ]);
    $this->template->category=$category;
  }

  /**
   * Formulář na editaci kategorií
   * @return Form
   */
  public function createComponentCategoryEditForm():Form {
    $form = new Form();
    $form->addHidden('category_id');
    $form->addText('title','Název kategorie')
      ->setRequired('Musíte zadat název kategorie');
    $form->addTextArea('description','Popis kategorie')
      ->setRequired(false);
    $form->addSubmit('save','uložit')
      ->setHtmlAttribute('class','btn btn-primary')
      ->onClick[]=function(SubmitButton $button){
        //hodnoty z formuláře získáme v podobě pole
        /** @var array $values */
        $values=$button->form->getValues(true);

        //provedení potřebné akce
        if (!empty($values['category_id'])){
            $cat = $this->categoriesFacade->getCategory((int)$values['category_id']);

        }else{
            $cat = new Category();
        }

        $cat->description = $values['description'];
        $cat->title = $values['title'];

        $this->categoriesFacade->saveCategory($cat);

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

  public function injectCategoriesFacade(CategoriesFacade $categoriesFacade){
    $this->categoriesFacade=$categoriesFacade;
  }
}
