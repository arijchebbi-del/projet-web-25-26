<?php
require_once __DIR__ . '/../repository/contactRepository.php';

class contactService {
    private contactRepository $repository;

    public function __construct() {
        $this->repository = new contactRepository();
    }

    public function saveContactMessage(array $data): bool {
        // Basic validation
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['message'])) {
            return false; // Validation failed
        }

        // Save data using the repository
        return $this->repository->saveData($data);
    }
}
?>
