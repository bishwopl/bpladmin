<?php

namespace BplAdmin\Mapper;

use BplAdmin\Contract\UserMapperInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityRepository;

class UserMapper implements UserMapperInterface {
    
    public function __construct(private ObjectManager $om, private string $objectClassName) {}
    
    public function findByEmail(string $email): ?\CirclicalUser\Provider\UserInterface {
        return $this->om->getRepository($this->objectClassName)->findOneBy([
            'email' => $email
        ]);
    }

    public function getTotalUserCount(): int {
        if($this->om instanceof EntityManager){
            return $this->om->getRepository($this->objectClassName)->count([]);
        }elseif($this->om instanceof DocumentManager){
            return $this->om->createQueryBuilder($this->objectClassName)->count()->getQuery()->execute();
        }
    }

    public function getUser($userId): ?\CirclicalUser\Provider\UserInterface {
        return $this->om->find($this->objectClassName, $userId);
    }

    public function getUsers(int $offset = 0, int $limit = 0): iterable {
        return $this->om->getRepository($this->objectClassName)->findBy([],null,$limit,$offset);
    }

    public function save(object $entity): void {
        $this->om->persist($entity);
        $this->om->flush();
    }

    public function update(object $entity): void {
        $this->om->merge($entity);
        $this->om->flush();
    }
    
    public function delete(object $entity): void {
        $this->om->remove($entity);
        $this->om->flush();
    }
}

