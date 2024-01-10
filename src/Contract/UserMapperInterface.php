<?php

namespace BplAdmin\Contract;

use CirclicalUser\Provider\UserProviderInterface;

interface UserMapperInterface extends UserProviderInterface {
    
    public function delete(object $entity): void;

    public function getTotalUserCount(): int;

    public function getUsers(int $offset = 0, int $limit = 0): iterable;
}
