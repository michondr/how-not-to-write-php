<?php

namespace App\Model\Facades;

use App\Model\Entities\Category;
use App\Model\Entities\Note;
use App\Model\Repositories\CategoriesRepository;
use App\Model\Repositories\NotesRepository;

/**
 * Class CategoriesFacade - fasáda pro využívání kategorií z presenterů
 * @package App\Model\Facades
 */
class NoteFacade{

  private /*NotesRepository*/ $notesRepository;

  public function __construct(NotesRepository $notesRepository){
    $this->notesRepository=$notesRepository;
  }

  /**
   * Metoda pro načtení jedné poznámky
   * @param int $id
   * @return Note
   * @throws \Exception
   */
  public function getNote(int $id):Note {
    return $this->notesRepository->find($id);
  }

  /**
   * Metoda pro vyhledání notes
   * @param array|null $params = null
   * @param int $offset = null
   * @param int $limit = null
   * @return Category[]
   */
  public function findNotes(array $params=null,int $offset=null,int $limit=null):array {
    return $this->notesRepository->findAllBy($params,$offset,$limit);
  }
//
//  /**
//   * Metoda pro zjištění počtu kategorií
//   * @param array|null $params
//   * @return int
//   */
//  public function findCategoriesCount(array $params=null):int {
//    return $this->notesRepository->findCountBy($params);
//  }

  /**
   * Metoda pro uložení poznámky
   * @param Note &$note
   * @return bool
   */
  public function saveNote(Note &$note):bool {
    return (bool)$this->notesRepository->persist($note);
  }


}