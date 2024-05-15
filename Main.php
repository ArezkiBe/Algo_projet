<?php

## constante permettant l'accès au fichier source ##
define("BIBLIOTHEQUE","Bibliotheque.json");

## Fonction principale permetant d'afficher le menu et de gérer les input utilisateur ##
## paramètre mod a 0 par défaut permet de gérer l'affichage ou non de l'entete de démarage ##
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

    ## Récupération de l'input utilisateur ##
    $choice = fgets(STDIN);

    ## Gestion et lancement des différents scripts a partir des inputs  ##
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
        # code...
    }elseif ($choice == "x" || $choice == "X") {
        exit;
    }
}

## Fonction permetant de Créer un livre et l'ajouter au fichier bibliothèque  ##
function CreateBook(): void
{
    print_r("Début de la création du Livre\n");

    ## Génération de l'id unique du livre ##
    $id = uniqid("book_", true);

    ## Récupération des différents paramètres du livre##
    print_r("Entrez le nom du livre : \n");
    $name = fgets(STDIN);
    print_r("Entrez la description du livre : \n");
    $desc = fgets(STDIN);
    $stock = 0;
    ## Appel de la fonction stock permetant la gestion de l'affichage du menu des stock##
    SelectStock($stock);
 

    $book = [
        "id" => $id,
        "titre" => trim($name),
        "desc" => trim($desc),
        "stock" => $stock,
    ];


    ## Récupération des données de la bibliothèque et enregistrement du nouveau livre ##
    $file = json_decode(file_get_contents(BIBLIOTHEQUE),true);
    $file[] = $book;
    file_put_contents(BIBLIOTHEQUE,json_encode($file));

    print_r("Le livre a bien été créer !! \n");

    Menu(1);

}

## Fonction permetant de gerer le paramètre stock d'un livre avec son menu ##
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

## Fonction permettant d'afficher tout les livres de la bibliothèque ##
function DisplayAllBook($menu = 0)
{
    ## Récupération des données de la bibiliothèque  ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);
    if ($menu == 0 ) {
        print_r("-------------------------------------\n");
        print_r("Voici les livres de la bibliothèque : \n");
        print_r("-------------------------------------\n");
    }

    ## Boucle sur chacun des livres en affichant leurs parametre id et titre ##
    foreach ($decoded as $key => $value) {
        print_r("$key : ".$value["titre"] ." (id : ".$value["id"].")\n");
        print_r("-------------------------------------\n");
    }

    if ($menu == 0) {
        Menu(1);

    }

}

## Fonction permettant de selectionner et modifier un livre ##
function ModifyBook()
{
    ## Récupération des valeurs de la bibliotheque  ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez vous modifier ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    ## Récupération de l'input utilisateur ##
    $choice = trim(fgets(STDIN));

    ## Vérification de l'éistence du livre  ##
    if (array_key_exists($choice,$decoded)) 
    {
        foreach ($decoded[$choice] as $key => $value) 
        {
            ## Génération d'un nouvel id ##
            if ($key == "id") 
            {
                $param = uniqid("book_", true);

            }
            elseif($key == "stock") ## modification du stock ##
            {
                $param = 0;
                SelectStock($param);
                
            }else { ## Boucle sur les autres paramètres ##
                print_r("Entrez le nouveau paramètre (".$key."): ");
                $param = trim(fgets(STDIN));
            }
            
            $decoded[$choice][$key] = $param;
        }

        ## Enregistrement dans la bibliothèque ##
        file_put_contents(BIBLIOTHEQUE,json_encode($decoded));

        print_r("Le livre a bien été créer !! \n");

        Menu(1);

    }else{
        print_r("Aucun Livre correspondant !");
    }


}

## Fonction permettant de supprimer un livre de la bibliothèque##
function DeleteBook()
{
    ## Récupération des données de la bibliothèque ##
    $retrieve_data = file_get_contents(BIBLIOTHEQUE);

    $decoded = json_decode($retrieve_data,true);

    print_r("-------------------------------------\n");
    print_r("Quel livre voulez vous Supprimer ? : \n");
    print_r("-------------------------------------\n");
    DisplayAllBook(1);

    $choice = trim(fgets(STDIN));

    ## Vérification de l'existence du livre et suppression ##
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