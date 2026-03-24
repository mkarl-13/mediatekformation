<?php

namespace App\Controller\Admin;
use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controleur d'administration des formations
 *
 * @author Karl
 */
class AdminFormationsController extends AbstractController {

    private const FORMATIONS_TEMPLATE = "admin/formations.html.twig";

    /**
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;

    /**
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     * @param PlaylistRepository $playlistRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    /**
     * Affiche la liste de toutes les formations (espace admin)
     * @return Response
     */
    #[Route('/admin/formations', name: 'admin.formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_TEMPLATE, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * Supprime une formation après vérification du token CSRF
     * @param int $id
     * @param Request $request
     * @param FormationRepository $formationRepository
     * @return Response
     */
    #[Route('/admin/formations/{id}/delete', name: 'admin.formations.delete', methods: ['POST'])]
    public function delete(int $id, Request $request, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $formationRepository->remove($formation);
        }

        return $this->redirectToRoute('admin.formations');
    }

    /**
     * Affiche le formulaire d'une formation (création ou modification)
     * @param int|null $id
     * @return Response
     */
    #[Route('/admin/formations/formation/{id}', name: 'admin.formations.showone')]
    public function showOne(?int $id = null): Response {
        $formation = $id ? $this->formationRepository->find($id) : null;
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAll();

        return $this->render("admin/formation.html.twig", [
                    'formation' => $formation,
                    'categories' => $categories,
                    'playlists' => $playlists
        ]);
    }

    /**
     * Met à jour une formation après vérification du token CSRF
     * @param int $id
     * @param Request $request
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     * @param PlaylistRepository $playlistRepository
     * @return Response
     */
    #[Route('/admin/formations/{id}/edit', name: 'admin.formations.update', methods: ['POST'])]
    public function update(int $id, Request $request, FormationRepository $formationRepository, CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository): Response
    {
        $formation = $formationRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        $formation->setTitle($request->request->get('titre'));
        $formation->setDescription($request->request->get('description'));
        $formation->setVideoId($request->request->get('videoId'));
        $formation->setPublishedAt(new \DateTime($request->request->get('publishedAt')));

        $playlist = $playlistRepository->find($request->request->get('playlist'));
        $formation->setPlaylist($playlist);

        foreach ($formation->getCategories() as $categorie) {
            $formation->removeCategory($categorie);
        }

        foreach ($request->request->all('categories') as $categorieId) {
            $categorie = $categorieRepository->find($categorieId);
            $formation->addCategory($categorie);
        }

        if ($this->isCsrfTokenValid('edit' . $id, $request->request->get('_token'))) {
            $formationRepository->add($formation);
        }

        return $this->redirectToRoute('admin.formations');
    }

    /**
     * Crée une nouvelle formation après vérification du token CSRF
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/formations/create', name: 'admin.formations.create', methods: ['POST'])]
    public function create(Request $request): Response {
        $formation = new Formation();

        $formation->setTitle($request->request->get('titre'));
        $formation->setDescription($request->request->get('description'));
        $formation->setVideoId($request->request->get('videoId'));
        $formation->setPublishedAt(new \DateTime($request->request->get('publishedAt')));

        $playlist = $this->playlistRepository->find($request->request->get('playlist'));
        $formation->setPlaylist($playlist);

        foreach ($request->request->all('categories[]') as $categorieId) {
            $categorie = $this->categorieRepository->find($categorieId);
            $formation->addCategorie($categorie);
        }

        if ($this->isCsrfTokenValid('create', $request->request->get('_token'))) {
            $this->formationRepository->add($formation);
        }

        return $this->redirectToRoute('admin.formations');
    }

    /**
     * Affiche les formations triées sur un champ (espace admin)
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    #[Route('admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_TEMPLATE, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * Affiche les formations dont un champ contient la valeur recherchée (espace admin)
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_TEMPLATE, [
                    'formations' => $formations,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
     * Redirection de /admin vers /admin/formations
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function redirection(): Response {
        return $this->redirectToRoute('admin.formations');
    }
}
