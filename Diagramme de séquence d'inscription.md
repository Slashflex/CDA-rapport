title Diagramme de séquence d'inscription

actor Utilisateur
control RegistrationController
entity UserRepository
entity User (entity)
database Base de données

Utilisateur->RegistrationController: Acceder à la page d'inscription
activate Utilisateur
activate RegistrationController
RegistrationController-->Utilisateur:Retourner la page d'inscription
deactivate Utilisateur
deactivate RegistrationController

Utilisateur->RegistrationController:Remplir le formulaire
activate Utilisateur
activate RegistrationController

RegistrationController->UserRepository:Vérifier les informations
activate UserRepository

UserRepository->User (entity):Vérifier l'unicité de l'e-mail
activate User (entity)

User (entity)->Base de données:Vérifier l'unicité de l'e-mail

activate Base de données

Base de données-->User (entity):
deactivate Base de données

User (entity)-->UserRepository:

deactivate User (entity)
deactivate UserRepository
space 

activate UserRepository
UserRepository->User (entity):Vérifier la validité des champs
activate User (entity)
deactivate UserRepository
User (entity)->User (entity):Vérifier la validité des champs
deactivate User (entity)
User (entity)-->RegistrationController:

RegistrationController->RegistrationController:Encoder le mot de passe

RegistrationController->RegistrationController:Générer un token

RegistrationController->RegistrationController:Créer un dossier (nom-prenom) 

RegistrationController->RegistrationController:Définir avatar par défaut

RegistrationController->UserRepository:Persister les données 
activate UserRepository

UserRepository->User (entity):Persister les données
activate User (entity)
User (entity)->Base de données:Persister les données
activate Base de données
deactivate User (entity)

Base de données-->User (entity):Récupérer l'utilisateur (objet)
activate User (entity)

deactivate Base de données
User (entity)-->UserRepository:Récupérer l'utilisateur (objet)

deactivate User (entity)
UserRepository-->RegistrationController:Récupérer l'utilisateur (objet)
deactivate UserRepository

RegistrationController->RegistrationController:Envoyer un e-mail à l'utilisateur (avec token)

RegistrationController-->Utilisateur:Rediriger sur la page d'acceuil
RegistrationController-->Utilisateur:Notifier l'utilisateur de l'envoi d'un e-mail
deactivate RegistrationController

Utilisateur->Utilisateur:Cliquer sur le lien "confirmation" de l'e-mail

Utilisateur->RegistrationController:Confirmer l'inscription (token)
activate RegistrationController
RegistrationController->UserRepository:Chercher l'utilisateur (par son token)
activate UserRepository
UserRepository->User (entity):Chercher l'utilisateur (par son token)
activate User (entity)
User (entity)->Base de données:Chercher l'utilisateur (par son token)
deactivate User (entity)

activate Base de données
Base de données-->User (entity):Récupérer token
deactivate Base de données

activate User (entity)
User (entity)-->UserRepository:Récupérer token
deactivate User (entity)

UserRepository-->RegistrationController:Récupérer token
deactivate UserRepository

RegistrationController->RegistrationController:Définir le champ token à "null"

RegistrationController->UserRepository:Persister le champ token
activate UserRepository

UserRepository->User (entity):Persister le champ token
activate User (entity)
User (entity)->Base de données:Persister le champ token
deactivate User (entity)

activate Base de données
Base de données-->User (entity):Récupérer l'utilisateur (objet)
deactivate Base de données
activate User (entity)
User (entity)-->UserRepository:Récupérer l'utilisateur (objet)
deactivate User (entity)

UserRepository-->RegistrationController:Récupérer l'utilisateur (objet)
deactivate UserRepository
RegistrationController-->Utilisateur:Retourner page d'accueil (message flash)
deactivate RegistrationController