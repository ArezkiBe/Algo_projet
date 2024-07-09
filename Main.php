<?php

## Constante permettant l'accès au fichier source ##
define("BIBLIOTHEQUE", "Bibliotheque.json");

## Fonction permettant l'affichage du menu principal de l'application ##
function Menu($mod = 0): void
{
    if ($mod == 0) {
        print_r("-----------------------------------------\n");
        print_r("Bienvenue dans la bibliothèque ! \n");
        print_r("-----------------------------------------\n");
    }
    
    print_r("Que puis-je faire pour vous ? :\n\n");

    print_r("[1] : Créer un Livre \n");
    print_r("[2] : Modifier un Livre \n");
    print_r("[3] : Supprimer un Livre \n");
    print_r("[4] : Afficher tous les livres de la bibliothèque \n");
    print_r("[5] : Afficher un Livre \n");
    print_r("[6] : Rechercher un Livre \n");
    print_r("[X] : Quitter \n\n");

    ## Récupération de l'input utilisateur ##
    $choice = trim(fgets(STDIN));

    ## Gestion et lancement des différents scripts à partir des inputs ##
    if ($choice === "1") {
       CreateBook();
    } elseif ($choice === "2") {
        ModifyBook();
    } elseif ($choice === "3") {
        DeleteBook();
    } elseif ($choice === "4") {
        DisplayAllBook();
    } elseif ($choice === "5") {
        # code...
    } elseif ($choice === "6") {
        SearchBook();
    } elseif ($choice === "x" || $choice === "X") {
        exit;
    } else {
        print_r("Choix incorrect. Veuillez réessayer.\n");
        Menu(1);
    }
}

## Fonction permettant de créer un livre et l'ajouter au fichier bibliothèque ##
function CreateBook(): void
{
    print_r("Début de la création du Livre\n");

    ## Génération de l'id unique du livre ##
    $id = uniqid("book_", true);

    ## Récupération des différents paramètres du livre ##
    print_r("Entrez le nom du livre : \n");
    $name = trim(fgets(STDIN));
    print_r("Entrez la description du livre : \n");
    $desc = trim(fgets(STDIN));
    $stock = 0;
    ## Appel de la fonction stock permettant la gestion de l'affichage du menu des stocks ##
    SelectStock($stock);

    $book = [
        "id" => $id,
        "titre" => $name,
        "desc" => $desc,
        "stock" => $stock,
    ];

    ## Récupération des données de la bibliothèque et enregistrement du nouveau livre ##
    $file = json_decode(file_get_contents(BIBLIOTHEQUE), true);
    $file[] = $book;
    file_put_contents(BIBLIOTHEQUE, json_encode($file));

    print_r("Le livre a bien été créé !! \n");

    Menu(1);
}

## Fonction permettant de gérer le paramètre stock d'un livre avec son menu ##
function SelectStock(&$stock)
{
    print_r("Le livre est-il en stock : \n");
    print_r("[1] Oui | [2] Non\n");
    $stockChoice = trim(fgets(STDIN));

    if ($stockChoice === "1") {
        $stock = true;
        return $stock;
    } elseif ($stockChoice === "2") {
        $stock = false;
        return $stock;
    } else {
        print_r("Choix incorrect \n");
        SelectStock($stock);
    }
}

## Fonction permettant d'afficher tous les livres de la bibliothèque ##
function DisplayAllBook($menu = 0)
{
    ## Récupération des données de la bibliothèque ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data, true);
    if ($menu == 0) {
        print_r("-------------------------------------\n");
        print_r("Voici les livres de la bibliothèque : \n");
        print_r("-------------------------------------\n");
    }

    ## Boucle sur chacun des livres en affichant leurs paramètres id et titre ##
    foreach ($decoded as $key => $value) {
        $id = isset($value['id']) ? $value['id'] : 'N/A';
        print_r("$key : ".$value["titre"] ." (id : ".$value["id"].")\n");
        print_r("-------------------------------------\n");
    }

    if ($menu == 0) {
        Menu(1);
    }
}

## Fonction permettant de sélectionner et modifier un livre ##
function ModifyBook()
{
    ## Récupération des valeurs de la bibliothèque ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data, true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez-vous modifier ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    ## Récupération de l'input utilisateur ##
    $choice = trim(fgets(STDIN));

    ## Vérification de l'existence du livre ##
    if (array_key_exists($choice, $decoded)) {
        foreach ($decoded[$choice] as $key => $value) {
            ## Génération d'un nouvel id ##
            if ($key == "id") {
                $param = uniqid("book_", true);
            } elseif ($key == "stock") { ## modification du stock ##
                $param = 0;
                SelectStock($param);
            } else { ## Boucle sur les autres paramètres ##
                print_r("Entrez le nouveau paramètre (".$key."): ");
                $param = trim(fgets(STDIN));
            }
            
            $decoded[$choice][$key] = $param;
        }

        ## Enregistrement dans la bibliothèque ##
        file_put_contents(BIBLIOTHEQUE, json_encode($decoded));

        print_r("Le livre a bien été modifié !! \n");

        Menu(1);
    } else {
        print_r("Aucun Livre correspondant !\n");
        ModifyBook();
    }
}

## Fonction permettant de supprimer un livre de la bibliothèque ##
function DeleteBook()
{
    ## Récupération des données de la bibliothèque ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data, true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez-vous supprimer ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    $choice = trim(fgets(STDIN));

    ## Vérification de l'existence du livre et suppression ##
    if (array_key_exists($choice, $decoded)) {
        unset($decoded[$choice]);
        file_put_contents(BIBLIOTHEQUE, json_encode($decoded));
        print_r("Livre supprimé avec succès !\n");
        Menu(1);
    } else {
        print_r("Aucun Livre correspondant !\n");
        DeleteBook();
    }
}

## Fonction pour rechercher un livre ##
function SearchBook(): void
{
    $books = json_decode(file_get_contents(BIBLIOTHEQUE), true);

    print_r("Choisissez la colonne pour la recherche :\n");
    print_r("[1] : Nom\n");
    print_r("[2] : Description\n");
    print_r("[3] : Stock\n");
    print_r("[4] : ID\n");

    $columnChoice = trim(fgets(STDIN));
    $column = "";
    switch ($columnChoice) {
        case "1":
            $column = "titre";
            break;
        case "2":
            $column = "desc";
            break;
        case "3":
            $column = "stock";
            break;
        case "4":
            $column = "id";
            break;
        default:
            print_r("Choix incorrect. Veuillez réessayer...\n");
            SearchBook();  // Call SearchBook again to prompt the user for correct input
            return;
    }

    print_r("Entrez la valeur à rechercher :\n");
    $value = trim(fgets(STDIN));

    foreach ($books as $book) {
        if (isset($book[$column]) && $book[$column] == $value) {
            print_r("Livre trouvé :\n");
            print_r("ID: " . $book['id'] . "\n");
            print_r("Titre: " . $book['titre'] . "\n");
            print_r("Description: " . (isset($book['desc']) ? $book['desc'] : "N/A") . "\n");
            print_r("Stock: " . (isset($book['stock']) && $book['stock'] > 0 ? "Oui" : "Non") . "\n");
            break;
        }
    }
    Menu(1);  // Call Menu again to continue interaction
}

Menu();

## Fonction de recherche binaire ##
function binarySearch($array, $column, $value)
{
    $low = 0;
    $high = count($array) - 1;

    while ($low <= $high) {
        $mid = intdiv($low + $high, 2);
        if ($array[$mid][$column] < $value) {
            $low = $mid + 1;
        } elseif ($array[$mid][$column] > $value) {
            $high = $mid - 1;
        } else {
            return $mid;
        }
    }
    return -1;
}

## Fonction de tri rapide ##
function quickSort($array, $column)
{
    if (count($array) < 2) {
        return $array;
    }
    
    $left = $right = [];
    reset($array);
    $pivot_key = key($array);
    $pivot = array_shift($array);
    
    foreach ($array as $k => $v) {
        if ($v[$column] < $pivot[$column]) {
            $left[$k] = $v;
        } else {
            $right[$k] = $v;
        }
    }
    
    return array_merge(quickSort($left, $column), [$pivot_key => $pivot], quickSort($right, $column));
}

Menu();
?>
