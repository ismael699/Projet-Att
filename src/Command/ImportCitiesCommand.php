<?php 

namespace App\Command;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Annotation pour définir les propriétés de la commande
#[AsCommand(
    name: 'app:import-cities', 
    description: 'Importe les villes depuis l\'API geo gouv dans la base de données.' 
)] 

// commande : php bin/console app:import-cities 

class ImportCitiesCommand extends Command
{
    // Propriété statique pour le nom par défaut de la commande
    protected static $defaultName = 'app:import-cities';
    private $httpClient; // Propriété pour le client HTTP
    private $entityManager; // Propriété pour l'EntityManager

    // Constructeur de la classe, avec injection des dépendances
    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        parent::__construct(); // Appel du constructeur parent
        $this->httpClient = $httpClient; // Initialisation du client HTTP
        $this->entityManager = $entityManager; // Initialisation de l'EntityManager
    }

     // Configuration de la commande
    protected function configure(): void
    {
        $this
            ->setDescription('Importez des villes depuis Geo API Gouv et enregistrez-les dans la base de données.');
    }

    // Méthode d'exécution de la commande
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Création d'un objet SymfonyStyle pour afficher des messages bien formatés dans la console
        $io = new SymfonyStyle($input, $output);

        $url = 'https://geo.api.gouv.fr/communes?fields=nom,code,departement,surface&format=json&geometry=centre&limit=50&sort=surface';
        // Requête GET à l'API
        $response = $this->httpClient->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            $io->error('Échec de la récupération des données de l\'API'); // Affichage d'une erreur si le statut n'est pas 200
            return Command::FAILURE; // Retour d'un code d'échec
        }

         // Conversion de la réponse JSON en tableau PHP
        $citiesData = $response->toArray();

        // Boucle sur chaque ville retournée par l'API
        foreach ($citiesData as $cityData) {
            $city = new City(); // Création d'un nouvel objet City
            $city->setName($cityData['nom']); // Ajout d'un nom 
            $city->setCode($cityData['code']); // Ajout d'un code-postal 

            $this->entityManager->persist($city); // Persistance de l'objet City
        }

        // Énregistrement en base de données
        $this->entityManager->flush();

        // Affichage d'un message de succès
        $io->success('Les villes ont été importées avec succès.');
        return Command::SUCCESS; 
    }
}
