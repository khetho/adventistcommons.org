<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation as Api;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use \Exception;

/**
 * User
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
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
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository"))
 * @Api\ApiResource(
 *     attributes={
 *         "normalization_context"={
 *             "datetime_format" = "Y-m-d",
 *          },
 *     },
 *     collectionOperations={
 *         "get",
 *         "post",
 *     },
 *     itemOperations={
 *         "get",
 *         "delete",
 *         "put",
 *         "patch",
 *     },
 * )
 * @DoctrineAssert\UniqueEntity(
 *   "email",
 *   errorPath="email",
 *   message="This email is already in use.",
 * )
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
     * @var int
     *
     * @ORM\Column(name="facebook_id", type="integer", nullable=true)
     */
    private $facebookId;
    
    /**
     * @var int
     *
     * @ORM\Column(name="google_id", type="integer", nullable=true)
     */
    private $googleId;

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
     * @ORM\Column(name="forgotten_password_code", type="string", length=255, nullable=true)
     */
    private $resetPasswordCode;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="forgotten_password_time", type="integer", nullable=true)
     */
    private $resetPassTimestamp;
    private $resetPassDateTime;

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
     * @var Collection
     */
    private $skillsLinked;

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
     * The languages that user is granted to approve
     * @ORM\ManyToMany(targetEntity="Language")
     * @ORM\JoinTable(
     *      name="user_languages_approved",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     * )
     */
    private $langsHeCanApprove;

    /**
     * The languages that user is granted to approve
     * @ORM\ManyToMany(targetEntity="Language")
     * @ORM\JoinTable(
     *      name="user_languages_reviewable",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     * )
     */
    private $langsHeCanReview;

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
    
    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="members")
     */
    private $projects;
    
    /**
     * @ORM\OneToMany(targetEntity="ContentRevision", mappedBy="user")
     */
    private $contentRevisions;

    /**
     * @ORM\OneToMany(targetEntity="DownloadLog", mappedBy="user")
     */
    private $downloads;

    public function __construct(string $email)
    {
        $this->groups = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->langsHeCanApprove = new ArrayCollection();
        $this->langsHeCanReview = new ArrayCollection();
        $this->username = $email;
        $this->email = $email;
        $this->createdOn = date('U');
    }
    
    public function __toString()
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebookId(): ?int
    {
        return $this->facebookId;
    }

    public function setFacebookId($facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getGoogleId(): ?int
    {
        return $this->googleId;
    }

    public function setGoogleId($googleId): self
    {
        $this->googleId = $googleId;

        return $this;
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

    public function getResetPasswordCode(): ?string
    {
        return $this->resetPasswordCode;
    }

    public function setResetPasswordCode(?string $resetPasswordCode): self
    {
        $this->resetPasswordCode = $resetPasswordCode;

        return $this;
    }

    public function getResetPassTimestamp(): ?\DateTime
    {
        if (!$this->resetPassDateTime) {
            $this->resetPassDateTime = \DateTime::createFromFormat('U', $this->resetPassTimestamp);
            $this->resetPassDateTime = $this->resetPassDateTime ? $this->resetPassDateTime : null;
        }
        return $this->resetPassDateTime;
    }

    public function setResetPassTimestamp(\DateTime $resetPassTimestamp): self
    {
        $this->resetPassTimestamp = $resetPassTimestamp->format('U');
        $this->resetPassDateTime = $resetPassTimestamp;

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
        return $this->getFirstName().($this->getFirstName() && $this->getFirstName() ? ' ' : '').$this->getLastName();
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

    public function getSkillsLinked(): Collection
    {
        return $this->skillsLinked;
    }

    public function addSkillLinked(Skill $skill): self
    {
        $this->addSkill($skill);

        return $this;
    }

    public function removeSkillLinked(Skill $skill): self
    {
        $this->removeSkill($skill);

        return $this;
    }

    public function getSkillsAdded(): array
    {
        return is_array($this->skillsAdded) ? $this->skillsAdded : [];
    }

    public function getSkills(): array
    {
        return array_merge($this->skillsLinked->toArray(), $this->getSkillsAdded());
    }

    public function addSkill($skill): self
    {
        if (!$skill) {
            return $this;
        }
        if ($skill instanceof Skill) {
            if (!$this->skillsLinked->contains($skill)) {
                $this->skillsLinked[] = $skill;
            }

            return $this;
        } elseif (is_string($skill)) {
            if (!in_array($skill, $this->getSkillsAdded())) {
                $this->skillsAdded[] = $skill;
            }

            return $this;
        }

        throw new Exception(sprintf('Skill must be a string or a Skill object. %s given.', gettype($skill)));
    }

    public function removeSkill($skill): self
    {
        if ($skill instanceof Skill) {
            if ($this->skillsLinked->contains($skill)) {
                $this->skillsLinked->removeElement($skill);
            }

            return $this;
        } elseif (is_string($skill)) {
            $index = array_search($skill, $this->skillsAdded);
            if ($index !== null) {
                unset($this->skillsAdded[$index]);
            }

            return $this;
        }

        throw new Exception('Skill must be a string or a Skill object');
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

    public function getAllLanguages(): Collection
    {
        $languages = $this->getLanguages();
        $languages->add($this->getMotherLanguage());

        return $languages;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLangsHeCanApprove(): Collection
    {
        return $this->langsHeCanApprove;
    }

    public function addLanguageHeCanApprove(Language $language): self
    {
        if (!$this->langsHeCanApprove->contains($language)) {
            $this->langsHeCanApprove[] = $language;
        }

        return $this;
    }

    public function removeLanguageHeCanApprove(Language $language): self
    {
        if ($this->langsHeCanApprove->contains($language)) {
            $this->langsHeCanApprove->removeElement($language);
        }

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLangsHeCanReview(): Collection
    {
        return $this->langsHeCanReview;
    }

    public function addLanguageHeCanReview(Language $langsHeCanApprove): self
    {
        if (!$this->langsHeCanReview->contains($langsHeCanApprove)) {
            $this->langsHeCanReview[] = $langsHeCanApprove;
        }

        return $this;
    }

    public function removeLanguageHeCanReview(Language $langsHeCanApprove): self
    {
        if ($this->langsHeCanReview->contains($langsHeCanApprove)) {
            $this->langsHeCanReview->removeElement($langsHeCanApprove);
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
     * @return string[] The user roles
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
    
    public function getProjects()
    {
        return $this->projects;
    }
    
    public function getContentRevisions()
    {
        return $this->contentRevisions;
    }

    public function getDownloads()
    {
        return $this->downloads;
    }
}
