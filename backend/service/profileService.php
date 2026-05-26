<?php 
require_once __DIR__ . '/../repository/userRepository.php';
require_once __DIR__ . '/../config/ConnexionDB.php';
class profileService {
    private $repo;

    public function __construct() {
        $this->repo=new userRepository();
    }

    public function getFullProfile(int $userId): array {
        $user = $this->repo->findById($userId);
        if (!$user) {
            throw new Exception("Utilisateur introuvable.");
        }
        $skills=$this->repo->findskills($userId);
        $experiences=$this->repo->findExperiences($userId);
        $projects=$this->repo->findProjects($userId);
        $achievements=$this->repo->findAchievements($userId);
        $recommendations=$this->repo->findRecommendations($userId);
        $posts= $this->repo->findPosts($userId); 
        return [
            'user'            => $user,
            'skills'          => $skills,
            'experiences'     => $experiences,
            'projects'        => $projects,
            'achievements'    => $achievements,
            'recommendations' => $recommendations,
            'posts'           =>$posts

        ];
    }

    public function saveProfile(int $userId, array $post): void {
        $pdo = ConnexionDB::getInstance();
        $pdo->beginTransaction();
        try {
            $this->repo->updateUser($userId, [
                'bio'           => $post['bio'] ?? null,
                'tagline'       => $post['tagline'] ?? null,
                'github_link'   => $post['github_link'] ?? null,
                'linkedin_link' => $post['linkedin_link'] ?? null,
                'profile_link'  => $post['profile_link'] ?? null,
            ]);
        $this->repo->updateInsatien($userId, [
                'nom'        => $post['nom'] ?? '',
                'prenom'     => $post['prenom'] ?? '',
                'promo_year' => $post['promo_year'] ?? null,
                'parcours_id' => isset($post['parcours_id']) && $post['parcours_id'] !== ''
                    ? (int)$post['parcours_id']
                    : null,
            ]);
        $this->repo->syncSkills($userId, $post['skills'] ?? []);
        $this->repo->syncExperiences($userId, $post['experiences'] ?? []);
        $this->repo->syncProjects($userId, $post['projects'] ?? []);
        $this->repo->syncAchievements($userId, $post['achievements'] ?? []);
        $pdo->commit();
        }catch(Exception $e){
            $pdo->rollBack();
            throw $e;

        }

    }

    public function addRecommendation(int $fromUser, int $toUser, string $texte): void {
       if ($fromUser === $toUser) {
            throw new Exception("Vous ne pouvez pas vous recommander vous-même!!!!!");
        }
        $texte = trim($texte);
        if (empty($texte)){
            throw new Exception("Le texte de la recommandation ne peut pas être vide.");
        }
        $this->repo->addRecommendation($fromUser,$toUser,$texte);

    }
}
