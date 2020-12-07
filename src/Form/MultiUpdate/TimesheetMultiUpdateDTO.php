<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\MultiUpdate;

use App\Entity\Activity;
use App\Entity\Customer;
use App\Entity\EntityWithMetaFields;
use App\Entity\MetaTableTypeInterface;
use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\TimesheetMeta;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @App\Validator\Constraints\TimesheetMultiUpdate
 */
class TimesheetMultiUpdateDTO extends MultiUpdateTableDTO implements EntityWithMetaFields
{
    /**
     * @var Tag[]|ArrayCollection|iterable
     */
    private $tags = [];
    /**
     * @var bool
     */
    private $replaceTags = false;
    /**
     * @var bool
     */
    private $recalculateRates = false;
    /**
     * @var Customer|null
     */
    private $customer;
    /**
     * @var Project|null
     */
    private $project;
    /**
     * @var Activity|null
     */
    private $activity;
    /**
     * @var User|null
     */
    private $user;
    /**
     * @var bool|null
     */
    private $exported = null;
    /**
     * @var float|null
     */
    private $fixedRate = null;
    /**
     * @var float|null
     */
    private $hourlyRate = null;
    /**
     * @var TimesheetMeta[]|Collection
     */
    private $meta;
    /**
     * @var string[]
     */
    private $updateMeta = [];

    public function __construct()
    {
        $this->meta = new ArrayCollection();
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): TimesheetMultiUpdateDTO
    {
        $this->customer = $customer;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): TimesheetMultiUpdateDTO
    {
        $this->project = $project;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): TimesheetMultiUpdateDTO
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return Tag[]|ArrayCollection|iterable
     */
    public function getTags(): iterable
    {
        return $this->tags;
    }

    public function setTags(iterable $tags): TimesheetMultiUpdateDTO
    {
        $this->tags = $tags;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): TimesheetMultiUpdateDTO
    {
        $this->user = $user;

        return $this;
    }

    public function isExported(): ?bool
    {
        return $this->exported;
    }

    public function setExported(bool $exported): TimesheetMultiUpdateDTO
    {
        $this->exported = $exported;

        return $this;
    }

    public function isRecalculateRates(): bool
    {
        return $this->recalculateRates;
    }

    public function setRecalculateRates(bool $recalculateRates): TimesheetMultiUpdateDTO
    {
        $this->recalculateRates = $recalculateRates;

        return $this;
    }

    public function isReplaceTags(): bool
    {
        return $this->replaceTags;
    }

    public function setReplaceTags(bool $replaceTags): TimesheetMultiUpdateDTO
    {
        $this->replaceTags = $replaceTags;

        return $this;
    }

    public function getFixedRate(): ?float
    {
        return $this->fixedRate;
    }

    public function setFixedRate(?float $fixedRate): TimesheetMultiUpdateDTO
    {
        $this->fixedRate = $fixedRate;

        return $this;
    }

    public function getHourlyRate(): ?float
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(?float $hourlyRate): TimesheetMultiUpdateDTO
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    /**
     * @return TimesheetMeta[]|Collection
     */
    public function getMetaFields(): Collection
    {
        return $this->meta;
    }

    public function getMetaField(string $name): ?MetaTableTypeInterface
    {
        foreach ($this->meta as $field) {
            if (strtolower($field->getName()) === strtolower($name)) {
                return $field;
            }
        }

        return null;
    }

    public function setMetaField(MetaTableTypeInterface $meta): EntityWithMetaFields
    {
        $this->updateMeta[$meta->getName()] = $meta->getName();
        if (null === ($current = $this->getMetaField($meta->getName()))) {
            $this->meta->add($meta);

            return $this;
        }

        $current->merge($meta);

        return $this;
    }

    public function setUpdateMeta(array $names): EntityWithMetaFields
    {
        $this->updateMeta = $names;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getUpdateMeta(): array
    {
        return $this->updateMeta;
    }
}
