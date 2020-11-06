<?php

namespace App\Adapter\Line;

class LineUser
{
    /** @var string */
    private $id;

    /** @var string */
    private $displayName;

    /** @var string */
    private $pictureUrl;

    /**
     * User constructor.
     * @param string $id
     * @param string $displayName
     * @param string $pictureUrl
     */
    public function __construct(string $id, string $displayName, string $pictureUrl)
    {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->pictureUrl = $pictureUrl;
    }


    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getPictureUrl(): string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(string $pictureUrl): self
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

}
