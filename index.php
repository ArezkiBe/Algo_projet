<?php

define("BIBLIOTHEQUE", "Bibliotheque.json");
define("HISTORIQUE", "Historique.log");

class Book
{
  public $id;
  public $title;
  public $description;
  public $stock;

  public function __construct($title, $description, $stock)
  {
    $this->id = uniqid("book_", true);
    $this->title = $title;
    $this->description = $description;
    $this->stock = $stock;
  }
}

class Logger
{
  public static function log($message)
  {
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents(HISTORIQUE, "[$timestamp] $message\n", FILE_APPEND);
  }
}

class Library
{
  private $books;

  public function __construct()
  {
    $this->loadBooks();
  }

  private function loadBooks()
  {
    if (file_exists(BIBLIOTHEQUE)) {
      $content = file_get_contents(BIBLIOTHEQUE);
      $this->books = !empty($content) ? json_decode($content, true) : [];
    } else {
      $this->books = [];
    }
  }

  private function saveBooks()
  {
    file_put_contents(BIBLIOTHEQUE, json_encode($this->books));
  }

  public function addBook(Book $book)
  {
    $this->books[] = [
      'id' => $book->id,
      'title' => $book->title,
      'description' => $book->description,
      'stock' => $book->stock,
    ];
    $this->saveBooks();
    Logger::log("Ajout du livre : {$book->title}");
  }

  public function updateBook($id, $title, $description, $stock)
  {
    foreach ($this->books as &$book) {
      if ($book['id'] === $id) {
        $book['title'] = $title;
        $book['description'] = $description;
        $book['stock'] = $stock;
        $this->saveBooks();
        Logger::log("Modification du livre : {$title}");
        return true;
      }
    }
    return false;
  }

  public function deleteBook($id)
  {
    foreach ($this->books as $key => $book) {
      if ($book['id'] === $id) {
        Logger::log("Suppression du livre : {$book['title']}");
        unset($this->books[$key]);
        $this->saveBooks();
        return true;
      }
    }
    return false;
  }

  public function getBook($id)
  {
    foreach ($this->books as $book) {
      if ($book['id'] === $id) {
        Logger::log("Recherche du livre : {$book['title']}");
        return $book;
      }
    }
    return null;
  }

  public function getAllBooks()
  {
    Logger::log("Affichage de tous les livres");
    return $this->books;
  }

  public function sortBooks($column)
  {
    $this->mergeSort($this->books, 0, count($this->books) - 1, $column);
    $this->saveBooks();
    Logger::log("Tri des livres par : {$column}");
  }

  private function mergeSort(&$array, $left, $right, $column)
  {
    if ($left < $right) {
      $middle = $left + (int)(($right - $left) / 2);

      $this->mergeSort($array, $left, $middle, $column);
      $this->mergeSort($array, $middle + 1, $right, $column);

      $this->merge($array, $left, $middle, $right, $column);
    }
  }

  private function merge(&$array, $left, $middle, $right, $column)
  {
    $leftArray = array_slice($array, $left, $middle - $left + 1);
    $rightArray = array_slice($array, $middle + 1, $right - $middle);

    $i = 0;
    $j = 0;
    $k = $left;

    while ($i < count($leftArray) && $j < count($rightArray)) {
      $leftValue = strtolower($leftArray[$i][$column]);
      $rightValue = strtolower($rightArray[$j][$column]);

      if ($leftValue <= $rightValue) {
        $array[$k] = $leftArray[$i];
        $i++;
      } else {
        $array[$k] = $rightArray[$j];
        $j++;
      }
      $k++;
    }

    while ($i < count($leftArray)) {
      $array[$k] = $leftArray[$i];
      $i++;
      $k++;
    }

    while ($j < count($rightArray)) {
      $array[$k] = $rightArray[$j];
      $j++;
      $k++;
    }
  }
}

class LibraryUI
{
  private $library;

  public function __construct()
  {
    $this->library = new Library();
  }

  public function showMenu($mod = 0)
  {
    if ($mod == 0) {
      print_r("-----------------------------------------\n");
      print_r("Bienvenue dans la bibliothèque ! \n");
      print_r("-----------------------------------------\n");
    }

    print_r("Que puis-je faire pour vous ? :\n\n");
    print_r("[1] : Créer un livre \n");
    print_r("[2] : Modifier un livre \n");
    print_r("[3] : Supprimer un livre \n");
    print_r("[4] : Afficher tous les livres de la bibliothèque \n");
    print_r("[5] : Afficher un livre \n");
    print_r("[6] : Trier les livres \n");
    print_r("[7] : Consulter l'historique \n");
    print_r("[X] : Quitter \n\n");

    $choice = trim(fgets(STDIN));

    switch ($choice) {
      case 1:
        $this->createBook();
        break;
      case 2:
        $this->modifyBook();
        break;
      case 3:
        $this->deleteBook();
        break;
      case 4:
        $this->displayAllBooks();
        break;
      case 5:
        $this->displayBook();
        break;
      case 6:
        $this->sortBooks();
        break;
      case 7:
        $this->viewHistory();
        break;
      case 'x':
      case 'X':
        exit;
      default:
        print_r("Choix incorrect\n");
        $this->showMenu(1);
        break;
    }
  }

  private function createBook()
  {
    print_r("Début de la création du livre\n");

    print_r("Entrez le nom du livre : \n");
    $name = trim(fgets(STDIN));
    print_r("Entrez la description du livre : \n");
    $description = trim(fgets(STDIN));
    print_r("Le livre est-il en stock : \n");
    print_r("[1] Oui | [2] Non\n");
    $stock = trim(fgets(STDIN)) == 1;

    $book = new Book($name, $description, $stock);
    $this->library->addBook($book);

    print_r("Le livre a bien été créé !! \n");
    $this->showMenu(1);
  }

  private function modifyBook()
  {
    // print_r("Quel livre voulez-vous modifier ? : \n");
    // $this->displayAllBooks(1);

    print_r("Entrez l'identifiant du livre : \n");
    $id = trim(fgets(STDIN));

    $book = $this->library->getBook($id);
    if ($book) {
      print_r("Entrez le nouveau nom du livre (actuel: {$book['title']}): ");
      $name = trim(fgets(STDIN));
      print_r("Entrez la nouvelle description du livre (actuel: {$book['description']}): ");
      $description = trim(fgets(STDIN));
      print_r("Le livre est-il en stock (actuel: " . ($book['stock'] ? "Oui" : "Non") . ") : \n");
      print_r("[1] Oui | [2] Non\n");
      $stock = trim(fgets(STDIN)) == 1;

      if ($this->library->updateBook($id, $name, $description, $stock)) {
        print_r("Le livre a bien été modifié !! \n");
      } else {
        print_r("Erreur lors de la modification du livre !! \n");
      }
    } else {
      print_r("Aucun livre correspondant !\n");
    }
    $this->showMenu(1);
  }

  private function deleteBook()
  {
    print_r("Quel livre voulez-vous supprimer ? : \n");
    $this->displayAllBooks(1);

    print_r("Entrez l'identifiant du livre : \n");
    $id = trim(fgets(STDIN));

    if ($this->library->deleteBook($id)) {
      print_r("Livre supprimé avec succès !\n");
    } else {
      print_r("Aucun livre correspondant !\n");
    }
    $this->showMenu(1);
  }

  private function displayAllBooks($menu = 0)
  {

    $books = $this->library->getAllBooks();
    if (empty($books)) {
      print_r("-------------------------------------\n");
      print_r("Il n'y a aucun livre dans la bibliothèque.\n");
      print_r("-------------------------------------\n");
    } else {
      print_r("-------------------------------------\n");
      print_r("Voici les livres de la bibliothèque : \n");
      print_r("-------------------------------------\n");
      foreach ($books as $index => $book) {
        $disponibilite = $book['stock'] ? "oui" : "non";
        print_r(($index + 1) . " - \n");
        print_r("    id: " . $book['id'] . "\n");
        print_r("    titre: " . $book['title'] . "\n");
        print_r("    description: " . $book['description'] . "\n");
        print_r("    disponibilité: " . $disponibilite . "\n");
        print_r("-------------------------------------\n");
        print_r("-------------------------------------\n");
      }
    }

    if ($menu == 0) {
      $this->showMenu(1);
    }
  }

  private function displayBook()
  {
    print_r("Entrez l'identifiant du livre : \n");
    $id = trim(fgets(STDIN));

    $book = $this->library->getBook($id);
    if ($book) {
      echo "Titre: " . $book['title'] . "\n";
      echo "Description: " . $book['description'] . "\n";
      echo "Disponible: " . ($book['stock'] ? "Oui" : "Non") . "\n";
    } else {
      echo "Aucun livre trouvé\n";
    }
    $this->showMenu(1);
  }

  private function sortBooks()
  {
    print_r("Veuillez choisir la colonne à trier : \n");
    print_r("[1] : nom \n");
    print_r("[2] : description \n");
    print_r("[3] : disponibilité \n");

    $choice = trim(fgets(STDIN));
    $column = '';
    switch ($choice) {
      case 1:
        $column = 'title';
        break;
      case 2:
        $column = 'description';
        break;
      case 3:
        $column = 'stock';
        break;
      default:
        print_r("Numéro de colonne invalide \n");
        $this->showMenu(1);
        return;
    }


    $this->library->sortBooks($column);
    $this->displayAllBooks(1);

    $this->showMenu(1);
  }

  private function viewHistory()
  {
    if (file_exists(HISTORIQUE)) {
      print_r(file_get_contents(HISTORIQUE));
    } else {
      print_r("Aucun historique disponible.\n");
    }
    $this->showMenu(1);
  }
}

$libraryUI = new LibraryUI();
$libraryUI->showMenu();
