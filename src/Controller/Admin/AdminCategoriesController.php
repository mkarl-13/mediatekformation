<?php

namespace App\Controller\Admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Categorie;

/**
 * Controleur d'administration des catégories
 *
 * @author Karl
 */
class AdminCategoriesController extends AbstractController {

    private const CATEGORIES_TEMPLATE = "admin/categories.html.twig";

    /**
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * Affiche la liste de toutes les catégories triées par nom
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response {
        $categories = $this->categorieRepository->findAllOrderByName("ASC");
        return $this->render(self::CATEGORIES_TEMPLATE, [
            'categories' => $categories
        ]);
    }

    /**
     * Affiche les catégories triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
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

    /**
     * Affiche les catégories dont un champ contient la valeur recherchée
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/categories/recherche/{champ}', name: 'admin.categories.findallcontain')]
    public function findAllContain($champ, Request $request): Response {
        $valeur = $request->get("recherche");
        $categories = $this->categorieRepository->findByContainValue($champ, $valeur);
        return $this->render(self::CATEGORIES_TEMPLATE, [
                    'categories' => $categories,
                    'valeur' => $valeur
        ]);
    }

    /**
     * Crée une nouvelle catégorie si le nom n'existe pas déjà
     * @param Request $request
     * @return Response
     */
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

    /**
     * Supprime une catégorie si elle ne contient aucune formation
     * @param int $id
     * @param Request $request
     * @return Response
     */
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
