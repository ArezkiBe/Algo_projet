<?php

define("BIBLIOTHEQUE","Bibliotheque.json");

function Menu($mod = 0): void
{
    if ($mod == 0) {
        print_r("-----------------------------------------\n");
        print_r("Bienvenu dans la bibliothèque ! \n");
        print_r("-----------------------------------------\n");
    }
    
    print_r("Que puis-je faire pour vous ? :\n\n");

    print_r("[1] : Créer un Livre \n");
    print_r("[2] : Modifier un Livre \n");
    print_r("[3] : Supprimer un Livre \n");
    print_r("[4] : Afficher tous les livres de la bibliothèque \n");
    print_r("[5] : Afficher un Livre \n");
    print_r("[6] : Trier les livres \n");
    print_r("[X] : Quitter \n\n");

    $choice = fgets(STDIN);

    if ($choice == 1) 
    {
       CreateBook();
    }elseif ($choice == 2) {
        ModifyBook();
    }elseif ($choice == 3) {
        DeleteBook();
    }elseif ($choice == 4) {
        DisplayAllBook();
    }elseif ($choice == 5) {
        DisplayBook();
    }elseif ($choice == 6) {
        sortBooks();
    }elseif ($choice == "x" || $choice == "X") {
        exit;
    }
}


function CreateBook(): void
{
    print_r("Début de la création du Livre\n");


    $id = uniqid("book_", true);

    print_r("Entrez le nom du livre : \n");
    $name = fgets(STDIN);
    print_r("Entrez la description du livre : \n");
    $desc = fgets(STDIN);
    $stock = 0;
    SelectStock($stock);
 

    $book = [
        "id" => $id,
        "title" => trim($name),
        "desc" => trim($desc),
        "stock" => $stock,
    ];



    $file = json_decode(file_get_contents(BIBLIOTHEQUE),true);
    $file[] = $book;
    file_put_contents(BIBLIOTHEQUE,json_encode($file));

    print_r("Le livre a bien été créer !! \n");

    Menu(1);

}

function SelectStock(&$stock)
{
    print_r("Le livre est-il en stock : \n");
    print_r("[1] Oui | [2] Non\n");
    $stockChoice = fgets(STDIN);

    if ($stockChoice == 1) 
    {
        $stock = true;
        return $stock;
    }elseif ($stockChoice == 2) {
        $stock = false;
        return $stock;

    }else {
        print_r("Choix incorecte \n");
        SelectStock($stock);

    }

}

function DisplayAllBook($menu = 0)
{
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);
    if ($menu == 0 ) {
        print_r("-------------------------------------\n");
        print_r("Voici les livres de la bibliothèque : \n");
        print_r("-------------------------------------\n");
    }

    foreach ($decoded as $key => $value) {
        print_r("$key : ".$value["title"] ." (id : ".$value["id"].")\n");
        print_r("-------------------------------------\n");
    }

    if ($menu == 0) {
        Menu(1);

    }
}

function DisplayBook()
{
    print_r("Entrez l'identifiant du livre : \n");
    $id = fgets(STDIN);

    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    $found = false;
    

    foreach ($decoded as $key => $value) {
        if ($value['id'] == trim($id)) {
            echo "Titre: " . $value['title'] . "\n";
            echo "Description: " . $value['desc'] . "\n";
            echo "Disponible: " . ($value['stock'] ? "Oui" : "Non") . "\n";
            $found = true;
        }
    }

    if (! $found) {
        echo "Aucun livre trouvé\n";
    }

    system('cls');

    Menu(1);
}

function sortBooks()
{
    print_r("Veuillez choisir la colonne à trier : \n");
    print_r("[1] : nom \n");
    print_r("[2] : description \n");
    print_r("[3] : disponibilité \n");

    $column = '';
    $choice = trim(fgets(STDIN));
    if ($choice == 1) {
        $column = 'title';
    }elseif ($choice == 2) {
        $column = 'desc';
    }elseif ($choice == 3) {
        $column = 'stock';

    }else {
        print_r("Numéro de colonne invalide \n");
        Menu(1);
    }

    $retrieve_data = file_get_contents(BIBLIOTHEQUE);
    $decoded = json_decode($retrieve_data,true);

    mergeSort($decoded, 0, count($decoded) -1, $column);

    print_r($decoded);

    Menu(1);
}


function merge(&$array, $left, $middle, $right, $column)
{
    $leftLength = $middle - $left +1;
    $rightLength = $right - $middle;

    $leftArray = array_slice($array, $left, $leftLength);
    $rightArray = array_slice($array, $middle+1, $rightLength);


    $i = 0;
    $j = 0;
    $k = $left;

    while($i < $leftLength && $j < $rightLength) {
        if (strcmp($leftArray[$i][$column], $rightArray[$j][$column]) <= 0) {
            $array[$k] = $leftArray[$i];
            $i++;
        } else {
            $array[$k] = $rightArray[$j];
            $j++;
        }
        $k++;
    }

    while ($i < $leftLength) {
        $array[$k] = $leftArray[$i];
        $i++;
        $k++;
    }

    while ($j < $rightLength) {
        $array[$k] = $rightArray[$j];
        $j++;
        $k++;
    }
}

function mergeSort(&$array, $left, $right, $column)
{
    if ($left < $right) {
        $middle = $left + (int)(($right - $left) / 2);

        mergeSort($array, $left, $middle, $column);
        mergeSort($array, $middle+1, $right, $column);

        merge($array, $left, $middle, $right, $column);
    }
}

function ModifyBook()
{
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez vous modifier ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    $choice = trim(fgets(STDIN));

    if (array_key_exists($choice,$decoded)) 
    {
        foreach ($decoded[$choice] as $key => $value) 
        {
            if ($key == "id") 
            {
                $param = uniqid("book_", true);

            }elseif($key == "stock") {
                $param = 0;
                SelectStock($param);
                
            }else {
                print_r("Entrez le nouveau paramètre (".$key."): ");
                $param = trim(fgets(STDIN));
            }
            
            $decoded[$choice][$key] = $param;
        }


        file_put_contents(BIBLIOTHEQUE,json_encode($decoded));

        print_r("Le livre a bien été modifié !! \n");

        Menu(1);

    }else{
        print_r("Aucun Livre correspondant !");
    }


}

function DeleteBook()
{
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez vous Supprimer ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    $choice = trim(fgets(STDIN));
    if (array_key_exists($choice,$decoded)) 
    {
        unset($decoded[$choice]);
        print_r("Livre supprimer avec succès !");
        Menu(1);

    }else{
        print_r("Aucun Livre correspondant !");
        DeleteBook();
    }
}
Menu();