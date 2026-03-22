<?php

namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class CategoriesController extends AbstractController {
    
    private const CATEGORIES_TEMPLATE = "admin/categories.html.twig";

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

    public function __construct(CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response {
        $categories = $this->categorieRepository->findAllOrderByName("ASC");
        return $this->render(self::CATEGORIES_TEMPLATE, [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/tri/{champ}/{ordre}', name: 'admin.categories.sort')]
    public function sort($champ, $ordre): Response {
        switch ($champ) {
            case "name":
                $categories = $this->categorieRepository->findAllOrderByName($ordre);
                break;
            case "formations":
                $categories = $this->categorieRepository->findAllOrderByFormationsCount($ordre);
                break;
            default:
                # Ne rien faire si le champ est inconnu
                break;
        }
        return $this->render(self::CATEGORIES_TEMPLATE, [
                    'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/recherche/{champ}', name: 'admin.categories.findallcontain')]
    public function findAllContain($champ, Request $request): Response {
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findByContainValue($champ, $valeur);
        return $this->render(self::CATEGORIES_TEMPLATE, [
                    'categories' => $categories,
                    'valeur' => $valeur
        ]);
    }

    #[Route("/admin/categories/categorie/create", name: "admin.categories.create", methods: ["POST"])]
    public function create(Request $request): Response {
        $name = $request->request->get("name");
        $categorie = new Categorie();
        $categorie->setName($name);
        if($this->categorieRepository->findOneBy(["name" => $name])) {
            throw $this->createAccessDeniedException("une catégorie existe déjà avec le même nom");
        }
        if ($this->isCsrfTokenValid('create', $request->request->get('_token'))) {
            $this->categorieRepository->add($categorie);
        }
        return $this->redirectToRoute('admin.categories');
    }
    
    #[Route('/admin/categories/categorie/{id}/delete', name: 'admin.categories.delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response {
        $categorie = $this->categorieRepository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException("catégorie introuvable");
        }
        if ($categorie->getFormations()->count() > 0) {
            throw $this->createAccessDeniedException("seul les catégories vides peuvent être supprimées");
        }
        if ($this->isCsrfTokenValid("delete".$id, $request->request->get("_token"))) {
            $this->categorieRepository->remove($categorie);
        }
        return $this->redirectToRoute('admin.categories');
    }
}
