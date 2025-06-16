<?php

require_once dirname(__DIR__) . "/classes/Gallery.php";

class GalleryRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getGalleryById($galleryId)
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
