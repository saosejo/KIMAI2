<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Configuration\ConfigLoaderInterface;
use App\Entity\Configuration;
use App\Form\Model\SystemConfiguration;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

/**
 * @extends \Doctrine\ORM\EntityRepository<Configuration>
 */
class ConfigurationRepository extends EntityRepository implements ConfigLoaderInterface
{
    private static $cacheByPrefix = [];
    private static $cacheAll = [];
    private static $initialized = false;

    public function clearCache()
    {
        static::$cacheByPrefix = [];
        static::$cacheAll = [];
        static::$initialized = false;
    }

    private function prefillCache()
    {
        if (static::$initialized === true) {
            return;
        }

        /** @var Configuration[] $configs */
        $configs = $this->findAll();
        foreach ($configs as $config) {
            $key = substr($config->getName(), 0, strpos($config->getName(), '.'));
            if (!\array_key_exists($key, static::$cacheByPrefix)) {
                static::$cacheByPrefix[$key] = [];
            }
            static::$cacheByPrefix[$key][] = $config;
            static::$cacheAll[] = $config;
        }
        static::$initialized = true;
    }

    public function saveConfiguration(Configuration $configuration)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($configuration);
        $entityManager->flush();
        $this->clearCache();
    }

    /**
     * @param string $prefix
     * @return Configuration[]
     */
    public function getConfiguration(?string $prefix = null): array
    {
        $this->prefillCache();

        if (null === $prefix) {
            return static::$cacheAll;
        }

        if (!\array_key_exists($prefix, static::$cacheByPrefix)) {
            return [];
        }

        return static::$cacheByPrefix[$prefix];
    }

    public function saveSystemConfiguration(SystemConfiguration $model)
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            foreach ($model->getConfiguration() as $configuration) {
                $entity = $this->findOneBy(['name' => $configuration->getName()]);
                $value = $configuration->getValue();

                if (null === $value && null !== $entity) {
                    $em->remove($entity);
                    continue;
                }

                if (null === $entity) {
                    $entity = new Configuration();
                    $entity->setName($configuration->getName());
                }

                // allow to use entity types
                if (\is_object($value) && method_exists($value, 'getId')) {
                    $value = $value->getId();
                }

                $entity->setValue($value);

                $em->persist($entity);
            }

            $em->flush();
            $em->commit();
        } catch (ORMException $ex) {
            $em->rollback();
            throw $ex;
        }

        $this->clearCache();
    }
}
