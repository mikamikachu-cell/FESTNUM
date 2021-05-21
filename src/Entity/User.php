<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
// use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="un compte avec cette adresse email existe déjà.")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $videoTitle;

    /**
     * @ORM\Column(type="string", length=1048)
     * @var string
     */
    private $videoDescription;

    /**
     * @Vich\UploadableField(mapping="user_videos", fileNameProperty="video")
     * @var File
     */
    private $videoFile;

    /**
     * @ORM\Column(type="string", length=1048)
     * @var string
     */
    private $videoFilename;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __toString()
    {
        return ucfirst($this->getFirstname()) . " " . mb_strtoupper($this->getLastname());
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreated()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of video
     *
     * @return  string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set the value of video
     *
     * @param  string  $video
     *
     * @return  self
     */
    public function setVideo(string $video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get the value of videoFile
     *
     * @return  File
     */
    public function getVideoFile()
    {
        return $this->videoFile;
    }

    /**
     * Set the value of videoFile
     *
     * @param  File  $videoFile
     *
     * @return  self
     */
    public function setVideoFile(File $videoFile)
    {
        $this->videoFile = $videoFile;

        return $this;
    }

    /**
     * Get the value of videoTitle
     *
     * @return  string
     */
    public function getVideoTitle()
    {
        return $this->videoTitle;
    }

    /**
     * Set the value of videoTitle
     *
     * @param  string  $videoTitle
     *
     * @return  self
     */
    public function setVideoTitle(string $videoTitle)
    {
        $this->videoTitle = $videoTitle;

        return $this;
    }

    /**
     * Get the value of videoDescription
     *
     * @return  string
     */
    public function getVideoDescription()
    {
        return $this->videoDescription;
    }

    /**
     * Set the value of videoDescription
     *
     * @param  string  $videoDescription
     *
     * @return  self
     */
    public function setVideoDescription(string $videoDescription)
    {
        $this->videoDescription = $videoDescription;

        return $this;
    }

    /**
     * Get the value of videoFilename
     *
     * @return  string
     */
    public function getVideoFilename()
    {
        return $this->videoFilename;
    }

    /**
     * Set the value of videoFilename
     *
     * @param  string  $videoFilename
     *
     * @return  self
     */
    public function setVideoFilename(string $videoFilename)
    {
        $this->videoFilename = $videoFilename;

        return $this;
    }
}
