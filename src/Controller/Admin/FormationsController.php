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
 * Description of FormationsController
 *
 * @author Karl
 */
class FormationsController extends AbstractController {
    
    private const FORMATIONS_TEMPLATE = "admin/formations.html.twig";
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     *
     * @var type
     */
    private $playlistRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }
    
    #[Route('/admin/formations', name: 'admin_formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_TEMPLATE, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }
    
    
    #[Route('/admin/formations/{id}/delete', name: 'admin_formations_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation introuvable.');
        }
        
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $formationRepository->remove($formation);
        }
        
        return $this->redirectToRoute('admin_formations');
    }
    
    #[Route('/admin/formations/formation/{id}', name: 'admin_formations_showone')]
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

    #[Route('/admin/formations/{id}/edit', name: 'admin_formations_update', methods: ['POST'])]
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

        return $this->redirectToRoute('admin_formations');
    }
    
    #[Route('/admin/formations/create', name: 'admin_formations_create', methods: ['POST'])]
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
        
        return $this->redirectToRoute('admin_formations');
    }
    
    #[Route('admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin_formations_sort')]
    public function sort($champ, $ordre, $table = ""): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS_TEMPLATE, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    #[Route('admin/formations/recherche/{champ}/{table}', name: 'admin_formations_findallcontain')]
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
}
