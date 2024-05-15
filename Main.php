<?php

define("BIBLIOTHEQUE","Bibliotheque.json");


// $book = array(
//     "test" => [
//         "name" => "un livre",
//         "author" => "un autheur",
//         "theme" => "un theme"
//     ]

// );

// $json_data = json_encode($book, JSON_PRETTY_PRINT);

// file_put_contents($filename, $json_data);


// $retrieve_data = file_get_contents($filename);

// $decoded = json_decode($retrieve_data,true);
// print_r($retrieve_data);

// print_r($decoded);

// $book2 = array(
//         "name" => "un livre",
//         "author" => "un autheur",
//         "theme" => "un theme"


// );



// print_r($decoded);


// foreach ($decoded as $book => $value) 
// {
//     print_r("le livre ".$book + 1 ." : \n");
//     foreach ($value as $key => $value) 
//     {
//         print_r(" $key :  $value\n");

//     }
//     print_r("-------------------------\n");
// }



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
    print_r("[4] : Afficher tout les livres de la bibliothèque \n");
    print_r("[5] : Afficher un Livre \n");
    print_r("[X] : Quitter \n\n");

    $choice = fgets(STDIN);

    if ($choice == 1) 
    {
       CreateBook();
    }elseif ($choice == 2) {
        # code...
    }elseif ($choice == 3) {
        # code...
    }elseif ($choice == 4) {
        DisplayAllBook();
    }elseif ($choice == 5) {
        # code...
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
        "titre" => trim($name),
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

function DisplayAllBook()
{
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);
    print_r("-------------------------------------\n");
    print_r("Voici les livres de la bibliothèque : \n");
    print_r("-------------------------------------\n");
    foreach ($decoded as $key => $value) {
        print_r("$key : ".$value["titre"] ." (id : ".$value["id"].")\n");
        print_r("-------------------------------------\n");
    }

    Menu(1);

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
            echo "Titre: " . $value['titre'] . "\n";
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
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    mergeSort($decoded, 0, count($decoded) -1);

    print_r($decoded);
}

function merge(&$array, $left, $middle, $right)
{
    $leftLength = $middle - $left +1;
    $rightLength = $right - $middle;

    $leftArray = array_slice($array, $left, $leftLength);
    $rightArray = array_slice($array, $middle+1, $rightLength);


    $i = 0;
    $j = 0;
    $k = $left;

    while($i < $leftLength && $j < $rightLength) {
        if (strcmp($leftArray[$i]['titre'], $rightArray[$j]['titre']) <= 0) {
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

    while ($j < $leftLength) {
        $array[$k] = $rightArray[$j];
        $j++;
        $k++;
    }
}

function mergeSort($array, $left, $right)
{
    if ($left < $right) {
        $middle = $left + (int)(($right + $left) / 2);
        //echo $middle;

        mergeSort($array, $left, $middle);
        mergeSort($array, $middle+1, $right);

        merge($array, $left, $middle, $right);
    }
}

Menu();