<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="uc_forgotten_password_selector",
 *             columns={"forgotten_password_selector"}
 *         ),
 *         @ORM\UniqueConstraint(
 *             name="uc_email",
 *             columns={"email"}
 *         ),
 *         @ORM\UniqueConstraint(
 *             name="uc_remember_selector",
 *             columns={"remember_selector"}
 *         ),
 *         @ORM\UniqueConstraint(
 *             name="uc_activation_selector",
 *             columns={"activation_selector"}
 *         )
 *     },
 *     indexes={
 *         @ORM\Index(
 *              name="mother_language_id",
 *              columns={"mother_language_id"}
 *         )
 *     }
 * )
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=45, nullable=false)
     */
    private $ipAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="activation_selector", type="string", length=255, nullable=true)
     */
    private $activationSelector;

    /**
     * @var string|null
     *
     * @ORM\Column(name="activation_code", type="string", length=255, nullable=true)
     */
    private $activationCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="forgotten_password_selector", type="string", length=255, nullable=true)
     */
    private $forgottenPasswordSelector;

    /**
     * @var string|null
     *
     * @ORM\Column(name="forgotten_password_code", type="string", length=255, nullable=true)
     */
    private $forgottenPasswordCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="forgotten_password_time", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $forgottenPasswordTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remember_selector", type="string", length=255, nullable=true)
     */
    private $rememberSelector;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remember_code", type="string", length=255, nullable=true)
     */
    private $rememberCode;

    /**
     * @var int
     *
     * @ORM\Column(name="created_on", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $createdOn;

    /**
     * @var int|null
     *
     * @ORM\Column(name="last_login", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $lastLogin;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=true)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="company", type="string", length=100, nullable=true)
     */
    private $company;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="text", length=65535, nullable=true)
     */
    private $bio;

    /**
     * Many User have Many Skills.
     * @ORM\ManyToMany(targetEntity="Skill")
     * @ORM\JoinTable(
     *      name="user_skills",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id")}
     * )
     */
    private $skills;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="product_notify", type="boolean", nullable=false)
     */
    private $productNotify = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="pro_translator", type="boolean", nullable=false)
     */
    private $proTranslator = '0';

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mother_language_id", referencedColumnName="id")
     * })
     */
    private $motherLanguage;
    
    /**
     * Many User have Many Language.
     * @ORM\ManyToMany(targetEntity="Language")
     * @ORM\JoinTable(
     *      name="user_languages",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     * )
     */
    private $languages;
    
    /**
     * Many User have Many Groups.
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(
     *      name="users_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->skills = new \Doctrine\Common\Collections\ArrayCollection();
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getActivationSelector(): ?string
    {
        return $this->activationSelector;
    }

    public function setActivationSelector(?string $activationSelector): self
    {
        $this->activationSelector = $activationSelector;

        return $this;
    }

    public function getActivationCode(): ?string
    {
        return $this->activationCode;
    }

    public function setActivationCode(?string $activationCode): self
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    public function getForgottenPasswordSelector(): ?string
    {
        return $this->forgottenPasswordSelector;
    }

    public function setForgottenPasswordSelector(?string $forgottenPasswordSelector): self
    {
        $this->forgottenPasswordSelector = $forgottenPasswordSelector;

        return $this;
    }

    public function getForgottenPasswordCode(): ?string
    {
        return $this->forgottenPasswordCode;
    }

    public function setForgottenPasswordCode(?string $forgottenPasswordCode): self
    {
        $this->forgottenPasswordCode = $forgottenPasswordCode;

        return $this;
    }

    public function getForgottenPasswordTime(): ?int
    {
        return $this->forgottenPasswordTime;
    }

    public function setForgottenPasswordTime(?int $forgottenPasswordTime): self
    {
        $this->forgottenPasswordTime = $forgottenPasswordTime;

        return $this;
    }

    public function getRememberSelector(): ?string
    {
        return $this->rememberSelector;
    }

    public function setRememberSelector(?string $rememberSelector): self
    {
        $this->rememberSelector = $rememberSelector;

        return $this;
    }

    public function getRememberCode(): ?string
    {
        return $this->rememberCode;
    }

    public function setRememberCode(?string $rememberCode): self
    {
        $this->rememberCode = $rememberCode;

        return $this;
    }

    public function getCreatedOn(): ?int
    {
        return $this->createdOn;
    }

    public function setCreatedOn(int $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getLastLogin(): ?int
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?int $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getProductNotify(): ?bool
    {
        return $this->productNotify;
    }

    public function setProductNotify(bool $productNotify): self
    {
        $this->productNotify = $productNotify;

        return $this;
    }

    public function getProTranslator(): ?bool
    {
        return $this->proTranslator;
    }

    public function setProTranslator(bool $proTranslator): self
    {
        $this->proTranslator = $proTranslator;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
        }

        return $this;
    }

    public function getMotherLanguage(): ?Language
    {
        return $this->motherLanguage;
    }

    public function setMotherLanguage(?Language $motherLanguage): self
    {
        $this->motherLanguage = $motherLanguage;

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }
}
