<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation as Api;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

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
 * @Api\ApiResource(
 *     attributes={
 *         "normalization_context"={
 *             "datetime_format" = "Y-m-d",
 *          },
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *         "put"={"security"="is_granted('ROLE_ADMIN')"},
 *         "patch"={"security"="is_granted('ROLE_ADMIN')"},
 *     },
 * )
 * @DoctrineAssert\UniqueEntity(
 *   "email",
 *   errorPath="email",
 *   message="This email is already in use."    )
 */
class User implements UserInterface
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
     * @ORM\Column(name="ip_address", type="string", length=45, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Ip()
     */
    private $ipAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 4,
     *      max = 50,
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     * @Api\ApiProperty(
     *     readable=false,
     *     writable=false,
     * )
     */
    private $password;

    /**
     * @var string
     * @Api\ApiProperty
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 5,
     *      max = 50,
     * )
     */
    private $plainPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=254, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
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
    private $forgottenPasswordTimeDateTime;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="integer", nullable=false, options={"unsigned"=true})
     * @Api\ApiProperty(
     *     writable=false,
     * )
     */
    private $createdOn;
    private $createdOnDateTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="integer", nullable=true, options={"unsigned"=true})
     * @Api\ApiProperty(
     *     writable=false,
     * )
     */
    private $lastLogin;
    private $lastLoginDateTime;

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
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     * )
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     * )
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="company", type="string", length=100, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     * )
     */
    private $company;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 6,
     *      max = 20,
     * )
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     * )
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="text", length=65535, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 2,
     *      max = 65535,
     * )
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
     * @ORM\Column(name="skills", type="phpserialize", nullable=true)
     */
    private $skillsAdded = [];
    
    /**
     * @var bool
     *
     * @ORM\Column(name="product_notify", type="boolean", nullable=false)
     */
    private $productNotify = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="pro_translator", type="boolean", nullable=false)
     */
    private $proTranslator = false;

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

    public function __construct(string $email)
    {
        $this->groups = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->username = $email;
        $this->email = $email;
        $this->createdOn = date('U');
    }
    
    public function __toString()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setRealPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
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

    public function getForgottenPasswordTime(): ?\DateTime
    {
        if (!$this->forgottenPasswordTimeDateTime) {
            $this->forgottenPasswordTimeDateTime = \DateTime::createFromFormat('U', $this->forgottenPasswordTime);
            $this->forgottenPasswordTimeDateTime = $this->forgottenPasswordTimeDateTime ? $this->forgottenPasswordTimeDateTime : null;
        }
        return $this->forgottenPasswordTimeDateTime;
    }

    public function setForgottenPasswordTime(\DateTime $forgottenPasswordTime): self
    {
        $this->forgottenPasswordTime = $forgottenPasswordTime->format('U');
        $this->forgottenPasswordTimeDateTime = $forgottenPasswordTime;

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

    public function getCreatedOn(): ?\DateTime
    {
        if (!$this->createdOnDateTime) {
            $this->createdOnDateTime = \DateTime::createFromFormat('U', $this->createdOn);
            $this->createdOnDateTime = $this->createdOnDateTime ? $this->createdOnDateTime : null;
        }
        return $this->createdOnDateTime;
    }

    public function getLastLogin(): ?\DateTime
    {
        if (!$this->lastLoginDateTime) {
            $this->lastLoginDateTime = \DateTime::createFromFormat('U', $this->lastLogin);
            $this->lastLoginDateTime = $this->lastLoginDateTime ? $this->lastLoginDateTime : null;
        }
        return $this->lastLoginDateTime;
    }

    public function setLastLogin(\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin->format('U');
        $this->lastLoginDateTime = $lastLogin;

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
    
    public function getFullName(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
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
     * @return array
     */
    public function getSkills(): array
    {
        return array_merge($this->skills->toArray(), $this->skillsAdded);
    }
    
    public function getSkillsAdded(): array
    {
        return $this->skillsAdded;
    }

    public function addSkill($skill): self
    {
        if ($skill instanceof Skill) {
            if (!$this->skills->contains($skill)) {
                $this->skills[] = $skill;
            }
        } elseif (is_string($skill)) {
            if (!in_array($skill, $this->skillsAdded)) {
                $this->skillsAdded[] = $skill;
            }
        } else {
            throw new \Exception('Skill must be a string or a Skill object');
        }

        return $this;
    }

    public function removeSkill($skill): self
    {
        if ($skill instanceof Skill) {
            if ($this->skills->contains($skill)) {
                $this->skills->removeElement($skill);
            }
        } elseif (is_string($skill)) {
            $index = array_search($skill, $this->skillsAdded);
            if ($index !== null) {
                unset($this->skillsAdded[$index]);
            }
        } else {
            throw new \Exception('Skill must be a string or a Skill object');
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

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $groups = ['ROLE_USER'];
        /** @var Group $group */
        foreach ($this->getGroups() as $group) {
            if ($group->getName() == 'admin') {
                $groups[] = 'ROLE_ADMIN';
            }
        }

        return $groups;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
    
    public function getImage()
    {
        return md5(strtolower(trim($this->email)));
    }

    public function forget(): self
    {
        $this->email = null;
        $this->setFirstName('Inactive');
        $this->setLastName('user');
        $this->setIpAddress(null);
        $this->username = null;
        $this->password = null;
        $this->setActive(false);
        $this->setLocation(null);
        $this->setBio(null);
        $this->setMotherLanguage(null);
        $this->skills  = null;
        $this->productNotify = null;
        $this->proTranslator = null;
        
        return $this;
    }
}
