<?php

/*
|--------------------------------------------------------------------------
| Protocoles cliniques par motif de consultation (section 5.3)
|--------------------------------------------------------------------------
|
| Entretien structuré piloté par l'agent : questions-clés posées une par une
| plutôt qu'un formulaire, appliquées de façon déterministe (section 5.3).
| Revus et approuvés par le comité clinique avant toute modification
| (section 3.1, « gouvernance médicale ») — ce fichier n'est qu'un point de
| départ, pas une validation médicale.
|
| Chaque protocole : un mot-clé de détection (section simpliste de
| correspondance sur le motif saisi par l'utilisateur) et une liste ordonnée
| de questions {code, text}.
|
*/

return [

    'fievre' => [
        'label' => 'Fièvre',
        'keywords' => ['fièvre', 'fievre', 'température', 'chaud'],
        'questions' => [
            ['code' => 'duree', 'text' => 'Depuis combien de temps as-tu de la fièvre ?'],
            ['code' => 'temperature', 'text' => 'Connais-tu ta température actuelle ?'],
            ['code' => 'symptomes_associes', 'text' => 'As-tu d\'autres symptômes (toux, maux de tête, douleurs) ?'],
            ['code' => 'antecedents', 'text' => 'As-tu des antécédents médicaux ou traitements en cours ?'],
        ],
    ],

    'toux' => [
        'label' => 'Toux',
        'keywords' => ['toux', 'tousse'],
        'questions' => [
            ['code' => 'duree', 'text' => 'Depuis combien de temps tousses-tu ?'],
            ['code' => 'type', 'text' => 'La toux est-elle sèche ou grasse (avec des crachats) ?'],
            ['code' => 'essoufflement', 'text' => 'As-tu des difficultés à respirer ou un essoufflement ?'],
            ['code' => 'fievre_associee', 'text' => 'As-tu aussi de la fièvre ?'],
        ],
    ],

    'douleur_abdominale' => [
        'label' => 'Douleur abdominale',
        'keywords' => ['ventre', 'abdomen', 'abdominale', 'estomac'],
        'questions' => [
            ['code' => 'localisation', 'text' => 'Où se situe la douleur exactement ?'],
            ['code' => 'duree', 'text' => 'Depuis combien de temps as-tu cette douleur ?'],
            ['code' => 'intensite', 'text' => 'Sur une échelle de 1 à 10, comment évaluerais-tu la douleur ?'],
            ['code' => 'symptomes_associes', 'text' => 'As-tu des nausées, vomissements ou troubles du transit ?'],
        ],
    ],

    'cephalee' => [
        'label' => 'Céphalée',
        'keywords' => ['tête', 'céphalée', 'cephalee', 'migraine'],
        'questions' => [
            ['code' => 'duree', 'text' => 'Depuis combien de temps as-tu mal à la tête ?'],
            ['code' => 'intensite', 'text' => 'Sur une échelle de 1 à 10, comment évaluerais-tu la douleur ?'],
            ['code' => 'signes_associes', 'text' => 'As-tu des troubles de la vision, nausées ou sensibilité à la lumière ?'],
            ['code' => 'declencheur', 'text' => 'Y a-t-il un évènement qui a déclenché ce mal de tête ?'],
        ],
    ],

    'troubles_digestifs' => [
        'label' => 'Troubles digestifs',
        'keywords' => ['digestif', 'diarrhée', 'diarrhee', 'vomissement', 'nausée', 'nausee'],
        'questions' => [
            ['code' => 'nature', 'text' => 'Décris tes symptômes : diarrhée, vomissements, ballonnements ?'],
            ['code' => 'duree', 'text' => 'Depuis combien de temps cela dure-t-il ?'],
            ['code' => 'hydratation', 'text' => 'Arrives-tu à t\'hydrater normalement ?'],
            ['code' => 'alimentation_recente', 'text' => 'As-tu mangé quelque chose d\'inhabituel récemment ?'],
        ],
    ],

];
