<?php

namespace App\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class PhpSerializeType extends Type
{
    const MYTYPE = 'phpserialize';
    
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (! $value) {
            return null;
        }

        return serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return [];
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        return unserialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
    
    public function getName()
    {
        return self::MYTYPE; // modify to match your constant name
    }
}
