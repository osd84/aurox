<?php
namespace App;

use OsdAurox\BaseModel;

class PostsModel extends BaseModel
{
    public const TABLE = "posts";
    public string $element = "post";


    // champs spécifiques
    public string $title;
    public ?string $content;
    public ?string $short;
    public ?string $imgSrc;
    public ?string $imgAlt;
    public ?string $linkFacebook;
    public ?string $linkLinkedin;
    public ?int $authorId;
    public ?int $categoryId;
    public ?string $status;
    public ?string $slug;
    public ?string $metaTitle;
    public ?string $metaDesc;
    public ?string $publishedAt;


    public function __construct(){

    }


    public function create(\PDO $pdo, $currentUserId): int
    {
        // Exemple d'implémentation.

        $stmt = "INSERT INTO " . static::TABLE .
                    " SET 
                          -- champ obligatoire  
                          created_at = :created_at,
                          updated_at = :updated_at,
                          created_by = :created_by,
                          updated_by = :updated_by,
                          -- champ propre à Post  
                          title = :title,
                          content = :content,
                          short = :short,
                          img_src = :img_src,
                          img_alt = :img_alt,
                          link_facebook = :link_facebook,
                          link_linkedin = :link_linkedin,
                          author_id = :author_id,
                          category_id = :category_id,
                          status = :status,
                          slug = :slug,
                          meta_title = :meta_title,
                          meta_desc = :meta_desc,
                          published_at = :published_at
        ";
        $stmt = $pdo->prepare($stmt);

        $stmt->execute([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'created_by' => (int) $currentUserId,
            'updated_by' => null,
            // champ propre à Post
            'title' => $this->title,
            'content' => $this->content,
            'short' => $this->short,
            'img_src' => $this->imgSrc,
            'img_alt' => $this->imgAlt,
            'link_facebook' => $this->linkFacebook,
            'link_linkedin' => $this->linkLinkedin,
            'author_id' => $this->authorId,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'slug' => $this->slug,
            'meta_title' => $this->metaTitle,
            'meta_desc' => $this->metaDesc,
            'published_at' => $this->publishedAt,
        ]);
        return $pdo->lastInsertId();
    }


    public function update(\PDO $pdo, int $id, $currentUserId): bool
    {
        // Exemple d'implémentation de la méthode update.
        $stmt = /** @lang MySQL */
            "UPDATE " . static::TABLE .
            " SET 
                      -- champ obligatoire
                      updated_at = :updated_at,
                      updated_by = :updated_by,
                      -- champ propre à Post
                      title = :title,
                      content = :content,
                      short = :short,
                      img_src = :img_src,
                      img_alt = :img_alt,
                      link_facebook = :link_facebook,
                      link_linkedin = :link_linkedin,
                      author_id = :author_id,
                      category_id = :category_id,
                      status = :status,
                      slug = :slug,
                      meta_title = :meta_title,
                      meta_desc = :meta_desc,
                      published_at = :published_at
                WHERE id = :id
    ";

        $stmt = $pdo->prepare($stmt);

        $success = $stmt->execute([
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => (int) $currentUserId,
            // champ propre à Post
            'title' => $this->title,
            'content' => $this->content,
            'short' => $this->short,
            'img_src' => $this->imgSrc,
            'img_alt' => $this->imgAlt,
            'link_facebook' => $this->linkFacebook,
            'link_linkedin' => $this->linkLinkedin,
            'author_id' => $this->authorId,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'slug' => $this->slug,
            'meta_title' => $this->metaTitle,
            'meta_desc' => $this->metaDesc,
            'published_at' => $this->publishedAt,
            'id' => $id,
        ]);

        return $success;
    }

    public function fetch(\PDO $pdo, int $id): bool
    {
        // Requête pour récupérer l'enregistrement par son ID
        $stmt = "SELECT * FROM " . static::TABLE . " WHERE id = :id LIMIT 1";

        $stmt = $pdo->prepare($stmt);
        $stmt->execute(['id' => $id]);

        // Récupérer les données sous forme associative
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            // Affectation manuelle des champs
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->content = $data['content'];
            $this->short = $data['short'];
            $this->imgSrc = $data['img_src'];
            $this->imgAlt = $data['img_alt'];
            $this->linkFacebook = $data['link_facebook'];
            $this->linkLinkedin = $data['link_linkedin'];
            $this->authorId = (int) $data['author_id'];
            $this->categoryId = (int) $data['category_id'];
            $this->status = (int) $data['status'];
            $this->slug = $data['slug'];
            $this->metaTitle = $data['meta_title'];
            $this->metaDesc = $data['meta_desc'];
            $this->publishedAt = $data['published_at'];
            $this->createdAt = $data['created_at'];
            $this->updatedAt = $data['updated_at'];
            $this->createdBy = (int) $data['created_by'];
            $this->updatedBy = $data['updated_by'] ? (int) $data['updated_by'] : null;

            return true; // Enregistrement trouvé et chargé
        }

        return false; // Enregistrement non trouvé
    }


    public function remove(\PDO $pdo, int $id): bool
    {
        // Requête SQL pour supprimer un enregistrement en fonction de son ID
        $stmt = "DELETE FROM " . static::TABLE . " WHERE id = :id";

        $stmt = $pdo->prepare($stmt);

        // Exécution de la requête en passant l'ID comme paramètre
        return $stmt->execute([
            'id' => $id,
        ]);
    }



}