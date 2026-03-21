<?php

namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Playlist;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController {
    
    private const PLAYLISTS_TEMPLATE = "admin/playlists.html.twig";

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

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

    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS_TEMPLATE, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response {
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "formations":
                $playlists = $this->playlistRepository->findAllOrderByFormationsCount($ordre);
                break;
            default:
                # Ne rien faire si le champ est inconnu
                break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS_TEMPLATE, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS_TEMPLATE, [
                    'playlists' => $playlists,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    #[Route('/admin/playlists/playlist/create', name: 'admin.playlists.create', methods: ['POST'])]
    public function create(Request $request): Response {
        $playlist = new Playlist();
        $playlist->setName($request->request->get('name'));
        $playlist->setDescription($request->request->get('description'));
        if ($this->isCsrfTokenValid('create', $request->request->get('_token'))) {
            $this->playlistRepository->add($playlist);
        }
        return $this->redirectToRoute('admin.playlists');
    }
    
    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOne(?int $id = null): Response {
        $playlist = $id ? $this->playlistRepository->find($id) : null;
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("admin/playlist.html.twig", [
                    'playlist' => $playlist,
                    'playlistcategories' => $playlistCategories,
                    'playlistformations' => $playlistFormations
        ]);
    }
    
    #[Route('/admin/playlists/playlist/{id}/delete', name: 'admin.playlists.delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException("playlist introuvable");
        }
        if ($playlist->getFormations()->count() > 0) {
            throw $this->createAccessDeniedException("seul les playlist vides peuvent être supprimées");
        }
        if ($this->isCsrfTokenValid("delete".$id, $request->request->get("_token"))) {
            $this->playlistRepository->remove($playlist);
        }
        return $this->redirectToRoute('admin.playlists');
    }
    
    #[Route('/admin/playlists/playlist/{id}/update', name: 'admin.playlists.update', methods: ['POST'])]
    public function update(int $id, Request $request): Response {
        $playlist = $this->playlistRepository->find($id);
        $playlist->setName($request->request->get('name'));
        $playlist->setDescription($request->request->get('description'));
        if ($this->isCsrfTokenValid("update" . $id, $request->request->get('_token'))) {
            $this->playlistRepository->add($playlist);
        }
        return $this->redirectToRoute('admin.playlists');
    }
}
