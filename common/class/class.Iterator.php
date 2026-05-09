<?php


 /**
 * Trida pro praci s objekty
 *
 *Prochazení objektu 
 *
 * <code>
 * $o1 = new Test();
 *$o1->jmeno = "Petr";
 *$o2 = new Test();
 *$o2->jmeno = "Ondra";
 *
 *$kolekce = new objectIterator();
 *
 *$kolekce->add($o1);
 *$kolekce->add($o2);
 *
 *$kolekce->first();
 *
 *while(!$kolekce->isDone()){
 * 
 *echo $kolekce->currentItem()->jmeno." !<br>";$kolekce->nextItem();
 *}
 * </code>
 *
 * @package    Pomocne
 */

class Iterator{

/**
 * pole objektu
 * @access protected
 * @var array
 */
protected $collection = array();

 /**
 * pridani noveho prvku
 * @param object $item novy objekt
 * @return void
 */
  public function add($item){
   
   $this->collection[] = $item;
   
  }
  
 /**
 * pridani celeho pole
 * @param array $items pole objektu
 * @return void
 */
  public function addAll($items){
   
   $this->collection = $items;
   
  }
  
 /**
 * vraci pocet prvku v poli
 * @return int
 */
  public function countItem(){
  
   return count($this->collection);
   
  }
  
 /**
 * vraci dalsi objekt
 * @return object
 */
  public function nextItem(){
  
    return (false !== next($this->collection));
  
  }
  
 /**
 * pretoci pole objektu na zacatek
 * @return void
 */
  public function first(){
  
   reset($this->collection);
   
  }
  
 /**
 * kontrola jestli jsme na konci pole
 * @return bool
 */
  public function isDone(){
  
    return (false === current($this->collection));
  
  }
  
 /**
 * vraci aktualni prvek
 * @return object
 */
  public function currentItem(){
  
    return current($this->collection);
  
  }
  
}






?>


