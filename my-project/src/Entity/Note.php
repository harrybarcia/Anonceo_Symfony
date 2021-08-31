<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $avis;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_enr;

    /**
     * @ORM\OneToMany(targetEntity=user::class, mappedBy="note")
     */
    private $user1;

    /**
     * @ORM\OneToMany(targetEntity=user::class, mappedBy="note")
     */
    private $user2;

    public function __construct()
    {
        $this->user1 = new ArrayCollection();
        $this->user2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAvis(): ?string
    {
        return $this->avis;
    }

    public function setAvis(string $avis): self
    {
        $this->avis = $avis;

        return $this;
    }

    public function getDateEnr(): ?\DateTimeInterface
    {
        return $this->date_enr;
    }

    public function setDateEnr(\DateTimeInterface $date_enr): self
    {
        $this->date_enr = $date_enr;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getUser1(): Collection
    {
        return $this->user1;
    }

    public function addUser1(user $user1): self
    {
        if (!$this->user1->contains($user1)) {
            $this->user1[] = $user1;
            $user1->setNote($this);
        }

        return $this;
    }

    public function removeUser1(user $user1): self
    {
        if ($this->user1->removeElement($user1)) {
            // set the owning side to null (unless already changed)
            if ($user1->getNote() === $this) {
                $user1->setNote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getUser2(): Collection
    {
        return $this->user2;
    }

    public function addUser2(user $user2): self
    {
        if (!$this->user2->contains($user2)) {
            $this->user2[] = $user2;
            $user2->setNote($this);
        }

        return $this;
    }

    public function removeUser2(user $user2): self
    {
        if ($this->user2->removeElement($user2)) {
            // set the owning side to null (unless already changed)
            if ($user2->getNote() === $this) {
                $user2->setNote(null);
            }
        }

        return $this;
    }
}
