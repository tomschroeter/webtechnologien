<?php

require_once dirname(__DIR__) . "/classes/Gallery.php";

class GalleryRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves a gallery by its unique ID.
     *
     * @param int $galleryId The ID of the gallery to retrieve.
     * @return ?Gallery The gallery corresponding to the given ID if found, null otherwise.
     *
     * @throws Exception If the gallery is not found in the database.
     */
    public function getGalleryById($galleryId): ?Gallery
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM galleries WHERE GalleryID = :galleryId";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("galleryId", $galleryId, PDO::PARAM_INT);
        $stmt->execute();

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->db->disconnect();

        if (!$record) {
            return null;
        }

        return Gallery::createGalleryFromRecord($record);
    }
}
