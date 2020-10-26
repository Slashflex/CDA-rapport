### Structure d'une application symfony

**assets**/ Ce dossier gère les fichiers statiques de type CSS, JS, images.

**bin/** Contient l'ensemble des comandes que l'on peut lancer.

**node_modules/ **Dossier inhérent à l'utilisation de Yarn ou Npm. Il contient l'ensemble des librairies de l'application installés via Yarn ou Npm.

**config/** Ce dossier est en quelque sorte le tableau de bord de l'application. Il contient l'ensemble des fichiers de configuration des services, bundles et autre modules.

**public/** Dossier de base de l'application. C'est en quelque sorte le point d'entrée, la racine publique du projet. 

​		**build/** Dossier contenant les scrips JS, styles CSS une fois compilé et minifiés par webpack.

**src/** Dossier qui contient l'ensemble du code de votre application. Il contient une structure de plusieurs dossiers :

​		**Controller/** Contient les fichier controller. Fichier avec rapport avec les traitement de l'application.

​		**DataFixtures/** Contient les jeux de données fictives pour faciliter le développement de fonctionnalités.

​		**Entity/** Contient des classes php qui servent à la représentation de la base de données.

​		**Form/** Contient des classes php permettant entre autre d'ajouter des contraintes de validations et des types sur des champs de formulaires.

​		**Migrations/** Fichier en liaison avec la base de données.

​		**Repository/** Contient les repository (selection de données sous forme de méthode dans la base de données).

​		**Security/** Contient des classes pour la logique d'authentification.

​		**Service/** Contient comme son nom l'indique, des services dédiés a une seule et unique tâche, dont on pourra se service dans des méthodes de classes d'un controlleur, par exemple l'envoi d'un e-mail.

**templates/**  L'ensemble des vues (affichage) de l'application.

**tests/**  Contient les fichier pour la gestion des tests unitaire et fonctionnels.

**var/**  Contient le cache et les logs de l'application.

**vendor/** Dossier inhérent à l'utilisation de Composer. Il contient l'ensemble des librairies de l'application installés via Composer.

**.env**  Fichier de configuration global de votre application. Il contient par exemple des variables global ou bien l'url d'accès à votre base de données.